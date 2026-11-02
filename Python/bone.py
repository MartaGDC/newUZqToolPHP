#!/bin/python3

import sys
import os
# os.environ['OPENBLAS_NUM_THREADS'] = '4'

import pyfeats as pf
import pywt as pw
from skimage import io, feature, measure, morphology
import numpy as np
from datetime import datetime

# Get the variables from command-line arguments
Name = sys.argv[1]
_x1 = int(sys.argv[2])
_y1 = int(sys.argv[3])
_x2 = int(sys.argv[4])
_y2 = int(sys.argv[5])
_height = int(sys.argv[6])
_width = int(sys.argv[7])
Evaluator = sys.argv[8]
Count = sys.argv[9]
Th = int(sys.argv[10])
Eval = sys.argv[11]
_scale = float(sys.argv[12])
user = sys.argv[13]
echo_type = sys.argv[14]

# Path to the image
BASE_DIR = "/var/www/html" if os.path.exists("/var/www/html/Upload") else os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
Imgspath = os.path.join(BASE_DIR, "Upload")
Selected = os.path.join(Imgspath, Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
if np.issubdtype(image.dtype, np.floating):
    image = np.clip(np.rint(image * 255), 0, 255).astype(np.uint8)  # Normalize to 0-255 range
elif image.dtype == np.uint16:
    image = (image // 256).astype(np.uint8)
else:
    image = np.asarray(image, dtype=np.uint8)

# Transform the coordinates into the true image
height, width = image.shape
scale = _scale * height

x1, x2 = sorted((int(round(_x1 * width / _width)), int(round(_x2 * width / _width))))
y1, y2 = sorted((int(round(_y1 * height / _height)), int(round(_y2 * height / _height))))
x1, x2 = max(0, x1), min(width, x2)
y1, y2 = max(0, y1), min(height, y2)

# Reshape the image to the ROI
ROI = image[y1:y2, x1:x2]
#print(x1, y1, x2, y2)

# Apply thresholding and morphological operations
binary_img = ROI > Th

# Perform morphological operations
erosion = morphology.binary_erosion(binary_img,footprint=np.ones((7,7)))
dilation = morphology.binary_dilation(erosion, footprint=morphology.ellipse(20,15))

# Apply the mask to the original image
hueso = ROI * dilation

io.imsave(os.path.join(Imgspath, os.path.splitext(Name)[0] + '-Bone.png'), hueso)

# Find contours
contours = measure.find_contours(dilation, 0.5)

cnt = [c for c in contours if len(c) > 4]


# Add time info
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")

A = measure.moments(dilation)[0,0]
Area = A / scale ** 2 if scale > 0 else 0.0
Per = measure.perimeter(dilation)/scale if scale > 0 else 0.0

hull = morphology.convex_hull_image(dilation)
hull_area = measure.moments(hull)[0,0]
Convex = A / hull_area if hull_area > 0 else 0.0

# Compute texture features
glcm = feature.graycomatrix(hueso, distances=[1], angles=[0, np.pi / 4, np.pi / 2, 3 * np.pi / 4], levels=256,
                            symmetric=True, normed=False).astype(np.float64)
glcm[0, :, :, :] = 0
glcm[:, 0, :, :] = 0
glcm_sum = glcm.sum(axis=(0, 1), keepdims=True)
glcm = np.divide(glcm, glcm_sum, out=np.zeros_like(glcm), where=glcm_sum > 0)
homogeneity = feature.graycoprops(glcm, 'homogeneity')[0].mean()
contrast = feature.graycoprops(glcm, 'contrast')[0].mean()
correlation = feature.graycoprops(glcm, 'correlation')[0].mean()

Point = [[x1, y1], [x2, y2]]

# Guardamos las variables
save_var = [time,Name,Eval,Evaluator,Count,len(cnt),Area,Per,Convex,homogeneity,contrast,correlation,Point]
###############

# print(*save_var[:5], sep='; ', end='; ')
# for v in save_var[5:8]:
#     print(f'{v:.4f}', end='; ')
# print('[...]')
print ("The bone processing has been successfully completed")

# Save data
path = os.path.join(BASE_DIR, "DATA", user, echo_type, "bone.txt")
os.makedirs(os.path.dirname(path), exist_ok=True)
with open(path, 'a') as file:
    line = ";".join(map(str, save_var))
    file.writelines(line + '\n')
