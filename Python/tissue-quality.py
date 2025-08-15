#!/bin/python3

import sys
import os
# sys.path.insert(0, '/home/ghc/.local/lib/python3.10/site-packages')
# os.environ['OPENBLAS_NUM_THREADS'] = '4'
# print(sys.path)

import pyfeats as pf
# import '/home/ghc/.local/lib/python3.10/site-packages/pyfeats' as pf
import pywt as pw
from skimage import io
import numpy as np
from datetime import datetime

# Get the variables from command-line arguments
Name = sys.argv[1]
_x1 = int(sys.argv[2])
_y1 = int(sys.argv[3])
_x2 = int(sys.argv[4])
_y2 = int(sys.argv[5])
_width = int(sys.argv[6])
_height = int(sys.argv[7])
Evaluator = sys.argv[8]
Count = sys.argv[9]
Eval = sys.argv[10]
user = sys.argv[11]
echo_type = sys.argv[12]

#print(Name, _x1, _y2, _width, Eval)

# Path to the image
Imgspath = "E:/UZqTool/uzqtool/html/Upload"
Selected = os.path.join(Imgspath, Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
image = (image * 255).astype(np.uint8)  # Normalize to 0-255 range

width, height = image.shape
x1 = int(_x1 * width / _width)
x2 = int(_x2 * width / _width)
y1 = int(_y1 * height / _height)
y2 = int(_y2 * height / _height)

# Reshape the image to the ROI
ROI = image[y1:y2, x1:x2]

# Medidas de la GLDM
features_GLCM, _, labels_GLCM, _ = pf.glcm_features(ROI, ignore_zeros=True)

# Añadimos las dos medidas de Haar wavelet
aux = ROI.astype(np.float32)
cA, (_, _, _) = pw.dwt2(aux, 'haar')
haar_mean = np.mean(cA)
haar_variance = np.var(cA)

# Medidas de forma
mask = np.ones(ROI.shape)
features_GLDS, labels_GLDS = pf.glds_features(ROI, mask, Dx=[0, 1, 1, 1], Dy=[1, 1, 0, -1])

# Save data
Point = [[x1,y1],[x2,y2]]

# Add time info
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")
glcm_ind = [1,5,3,9,2,4]
glcm = [features_GLCM[i] for i in glcm_ind]

save_var = [time,Name,Eval,Evaluator,Count,*glcm,*features_GLDS,haar_mean,haar_variance,Point]

folder = "E:/UZqTool/uzqtool/html/DATA/"+user
if not os.path.exists(folder):
    os.mkdir(folder)

subfolder = os.path.join(folder, echo_type)
if not os.path.exists(subfolder):
    os.mkdir(subfolder)
    
path = os.path.join(subfolder,"tissue-quality.txt")
# print(path)


with open(path, 'a') as file:
    line = ";".join(map(str, save_var))
    file.writelines(line+'\n')

# # Print the results
# print(f"{Name}; {Eval}; {Evaluator}; {Count}; ", end="")
# print(*[f"{v:.4f}" for v in features_GLCM[:4]], sep="; ", end="; ")
# print("[...]")

print ("The tissue processing has been successfully completed\nYou can go to the next tab")

