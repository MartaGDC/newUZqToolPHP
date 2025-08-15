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
Name = sys.argv[1]
_x1 = int(sys.argv[2])
_y1 = int(sys.argv[3])
_x2 = int(sys.argv[4])
_y2 = int(sys.argv[5])
_a1 = int(sys.argv[6])
_b1 = int(sys.argv[7])
_a2 = int(sys.argv[8])
_b2 = int(sys.argv[9])

_width = int(sys.argv[10])
_height = int(sys.argv[11])
Evaluator = sys.argv[12]
Count = sys.argv[13]
EvalU = sys.argv[14]
EvalD = sys.argv[15]
user = sys.argv[16]
echo_type = sys.argv[17]

# # # Load thi image
Imgspath = "E:/UZqTool/uzqtool/html/Upload"
Selected = os.path.join(Imgspath,Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
image = (image * 255).astype(np.uint8)  # Normalize to 0-255 range

# Transform the cordinates into true image
width, height = image.shape

x1 = int(_x1*width/_width)
x2 = int(_x2*width/_width)
y1 = int(_y1*height/_height)
y2 = int(_y2*height/_height)

a1 = int(_a1*width/_width)
a2 = int(_a2*width/_width)
b1 = int(_b1*height/_height)
b2 = int(_b2*height/_height)

# Reshape the image to the ROI
ROIS = [[]]*2
EVALS = [EvalU,EvalD]
POS = ['top', 'bottom']
POINTS =[[[x1,y1],[x2,y2]],[[a1,b1],[a2,b2]]]

ROIS[0] = image [y1:y2, x1:x2]
ROIS[1] = image [b1:b2, a1:a2]

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
    path = os.path.join("E:/UZqTool/uzqtool/html/DATA/",user,echo_type,"borders.txt")
    with open(path,'a') as file:
        line = ";".join(map(str, save_var))
        file.writelines(line+'\n')

print ("The border processing has been successfully completed")