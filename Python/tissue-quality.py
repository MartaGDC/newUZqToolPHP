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
echo_type = sys.argv[-1]
if echo_type == 'sarcopenia':
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
    recto = sys.argv[18]
    vasto = sys.argv[19]
    grasa = sys.argv[20]
    user = sys.argv[21]
else:
    Name = sys.argv[1]
    _x1 = int(sys.argv[2])
    _y1 = int(sys.argv[3])
    _x2 = int(sys.argv[4])
    _y2 = int(sys.argv[5])
    _height = int(sys.argv[6])
    _width = int(sys.argv[7])
    Evaluator = sys.argv[8]
    Count = sys.argv[9]
    Eval = sys.argv[10]
    user = sys.argv[11]

#print(_x1, _y1, _x2, _y2, _width, _height)
#print(Name, _x1, _y2, _width, Eval)

# Path to the image
Imgspath = "/var/www/html/Upload"
Selected = os.path.join(Imgspath, Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
image = (image * 255).astype(np.uint8)

height, width = image.shape
x1 = int(_x1 * width / _width)
x2 = int(_x2 * width / _width)
y1 = int(_y1 * height / _height)
y2 = int(_y2 * height / _height)

if echo_type == 'sarcopenia':
    a1 = int(_a1*width/_width)
    a2 = int(_a2*width/_width)
    b1 = int(_b1*height/_height)
    b2 = int(_b2*height/_height)

    c1 = int(_c1*width/_width)
    c2 = int(_c2*width/_width)
    d1 = int(_d1*height/_height)
    d2 = int(_d2*height/_height)

# Reshape the image to the ROI
if echo_type == "sarcopenia":
    ROIS = [None]* 3
    EVALS = [recto,vasto,grasa]
    POS = ['recto','vasto','grasa']
    POINTS = [[[x1,y1],[x2,y2]],[[a1,b1],[a2,b2]],[[c1,d1],[c2,d2]]]
    ROIS[0] = image [y1:y2, x1:x2]
    ROIS[1] = image [b1:b2, a1:a2]
    ROIS[2] = image [d1:d2, c1:c2]
else:
    Point = [[x1,y1],[x2,y2]]
    ROI = image[y1:y2, x1:x2]

folder = "/var/www/html/DATA/"+user
if not os.path.exists(folder):
    os.mkdir(folder)
subfolder = os.path.join(folder, echo_type)
if not os.path.exists(subfolder):
    os.mkdir(subfolder)
path = os.path.join(subfolder,"tissue-quality.txt")
# print(path)

if echo_type != 'sarcopenia':
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
    # Add time info
    now = datetime.now()
    time = now.strftime("%d/%m/%Y-%H:%M:%S")
    glcm_ind = [1,5,3,9,2,4]
    glcm = [features_GLCM[i] for i in glcm_ind]

    # Save data
    save_var = [time,Name,Eval,Evaluator,Count,*glcm,*features_GLDS,haar_mean,haar_variance,Point]
    with open(path, 'a') as file:
        line = ";".join(map(str, save_var))
        file.writelines(line+'\n')
else:
    for i,ROI in enumerate(ROIS):
        features_GLCM, _, labels_GLCM, _ = pf.glcm_features(ROI, ignore_zeros=True)
        aux = ROI.astype(np.float32)
        cA, (_, _, _) = pw.dwt2(aux, 'haar')
        haar_mean = np.mean(cA)
        haar_variance = np.var(cA)
        mask = np.ones(ROI.shape)
        features_GLDS, labels_GLDS = pf.glds_features(ROI, mask, Dx=[0, 1, 1, 1], Dy=[1, 1, 0, -1])
        now = datetime.now()
        time = now.strftime("%d/%m/%Y-%H:%M:%S")
        glcm_ind = [1,5,3,9,2,4]
        glcm = [features_GLCM[i] for i in glcm_ind]
        save_var = [time,Name,POS[i],EVALS[i],Evaluator,Count,*glcm,*features_GLDS,haar_mean,haar_variance,POINTS[i]]
        with open(path, 'a') as file:
            line = ";".join(map(str, save_var))
            file.writelines(line+'\n')

# # Print the results
# print(f"{Name}; {Eval}; {Evaluator}; {Count}; ", end="")
# print(*[f"{v:.4f}" for v in features_GLCM[:4]], sep="; ", end="; ")
# print("[...]")

print ("The tissue processing has been successfully completed\nYou can go to the next tab")

