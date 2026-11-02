#!/bin/python3

import sys
import os
# os.environ['OPENBLAS_NUM_THREADS'] = '4'

import pyfeats as pf
import pywt as pw
from skimage import io
import numpy as np

# Add time info
from datetime import datetime
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")

# Get the variables from command-line arguments
echo_type = sys.argv[-1]
if echo_type == 'menisco':
    Name = sys.argv[1]
    _x1 = int(sys.argv[2])
    _y1 = int(sys.argv[3])
    _x2 = int(sys.argv[4])
    _y2 = int(sys.argv[5])
    _a1 = int(sys.argv[6])
    _b1 = int(sys.argv[7])
    _a2 = int(sys.argv[8])
    _b2 = int(sys.argv[9])
    _c1 = int(sys.argv[10])
    _d1 = int(sys.argv[11])
    _c2 = int(sys.argv[12])
    _d2 = int(sys.argv[13])

    _height = int(sys.argv[14])
    _width = int(sys.argv[15])
    Evaluator = sys.argv[16]
    Count = sys.argv[17]
    menisco = sys.argv[18]
    femur = sys.argv[19]
    tibia = sys.argv[20]
    user = sys.argv[21]
else:
    Name = sys.argv[1]
    _x1 = int(sys.argv[2])
    _y1 = int(sys.argv[3])
    _x2 = int(sys.argv[4])
    _y2 = int(sys.argv[5])
    _a1 = int(sys.argv[6])
    _b1 = int(sys.argv[7])
    _a2 = int(sys.argv[8])
    _b2 = int(sys.argv[9])

    _height = int(sys.argv[10])
    _width = int(sys.argv[11])
    Evaluator = sys.argv[12]
    Count = sys.argv[13]
    EvalU = sys.argv[14]
    EvalD = sys.argv[15]
    user = sys.argv[16]

# # # Load thi image
BASE_DIR = "/var/www/html" if os.path.exists("/var/www/html/Upload") else os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
Imgspath = os.path.join(BASE_DIR, "Upload")
Selected = os.path.join(Imgspath,Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
if np.issubdtype(image.dtype, np.floating):
    image = np.clip(np.rint(image * 255), 0, 255).astype(np.uint8)  # Normalize to 0-255 range
elif image.dtype == np.uint16:
    image = (image // 256).astype(np.uint8)
else:
    image = np.asarray(image, dtype=np.uint8)

# Transform the cordinates into true image
height, width = image.shape

x1, x2 = sorted((int(round(_x1*width/_width)), int(round(_x2*width/_width))))
y1, y2 = sorted((int(round(_y1*height/_height)), int(round(_y2*height/_height))))

a1, a2 = sorted((int(round(_a1*width/_width)), int(round(_a2*width/_width))))
b1, b2 = sorted((int(round(_b1*height/_height)), int(round(_b2*height/_height))))

if echo_type == 'menisco':
    c1, c2 = sorted((int(round(_c1*width/_width)), int(round(_c2*width/_width))))
    d1, d2 = sorted((int(round(_d1*height/_height)), int(round(_d2*height/_height))))

# Reshape the image to the ROI
if echo_type == "menisco":
    ROIS = [None]* 3
    EVALS = [menisco,femur,tibia]
    POS = ['menisco','femur','tibia']
else:
    ROIS = [None]* 2
    EVALS = [EvalU,EvalD]
    POS = ['top', 'bottom']
if echo_type == 'menisco':
    POINTS =[[[x1,y1],[x2,y2]],[[a1,b1],[a2,b2]],[[c1,d1],[c2,d2]]]
else:
    POINTS =[[[x1,y1],[x2,y2]],[[a1,b1],[a2,b2]]]

ROIS[0] = image [y1:y2, x1:x2]
ROIS[1] = image [b1:b2, a1:a2]

if echo_type == 'menisco':
    ROIS[2] = image [d1:d2, c1:c2]

for i,ROI in enumerate(ROIS):
    # Medidas de la GLDM
    features_GLCM, _, labels_GLCM, _ = pf.glcm_features(ROI, ignore_zeros=True)

    # Añadimoslas dos medidas de Haar wavelet
    aux=np.array(ROI,dtype=np.float32)
    cA,(_,_,_) = pw.dwt2(aux,'haar')
    haar_mean=np.mean(cA)
    haar_variance=np.var(cA)

    # Medidas de forma

    mask=np.ones(ROI.shape)
    features_GLDS, labels_GLDS = pf.glds_features(ROI, mask, Dx=[0,1,1,1], Dy=[1,1,0,-1])


    # Guardamos las variables
    glcm_ind = [1,5,3,9,2,4]
    glcm = [features_GLCM[i] for i in glcm_ind]

    save_var = [time,Name,POS[i],EVALS[i],Evaluator,Count,*glcm,*features_GLDS,haar_mean,haar_variance,POINTS[i]]

    #save
    # print(*save_var[1:5],sep='; ',end='; ')
    # for v in save_var[6:8]:
    #     print(f'{v:.4f}', end='; ')
    # print('[...]')
   
    # Save data
    path = os.path.join(BASE_DIR, "DATA", user, echo_type, "borders.txt")
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path,'a') as file:
        line = ";".join(map(str, save_var))
        file.writelines(line+'\n')

print ("The border processing has been successfully completed")