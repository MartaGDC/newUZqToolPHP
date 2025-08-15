#!/bin/python3

import sys
import os
import json
# os.environ['OPENBLAS_NUM_THREADS'] = '4'

import pyfeats as pf
import pywt as pw
from skimage import io
import numpy as np
from datetime import datetime

# Get the variables from command-line arguments

Name = sys.argv[1]
_width = int(sys.argv[2])
_height = int(sys.argv[3])
Evaluator = sys.argv[4]
Count = sys.argv[5]
Eval = sys.argv[6]
_scale = float(sys.argv[7])
user = sys.argv[8]
echo_type = sys.argv[9]

# Cargamos puntos del fichero
polygon_path = "E:/UZqTool/uzqtool/html/PHP/polygon.txt"
with open(polygon_path, 'r') as file:
    points = file.readlines()[1:]
    _x = []
    _y = []
    for line in points:
        values = line.strip().split('\t')
        _x.append(float(values[0]))
        _y.append(float(values[1]))

# Path to the image
Imgspath = "E:/UZqTool/uzqtool/html/Upload"
Selected = os.path.join(Imgspath, Name)

# Transform the coordinates into the true image
image = io.imread(Selected, as_gray=True)
image = (image * 255).astype(np.uint8)  # Normalize to 0-255 range

# Transform the cordinates into true image
width, height = image.shape

x = list(map(int, [_xval * width / _width for _xval in _x]))
y = list(map(int, [_yval * height / _height for _yval in _y]))

# Set scale for each image

scale = _scale*height
#print(scale)

perimeter = np.transpose([x,y])

ms1=[]
dx1=[]
dy1=[]
ms2=[]
dx2=[]
dy2=[]
aux=False
for i in range(len(perimeter)):
    m = (perimeter[i][1]-perimeter[i-1][1])/(perimeter[i][0]-perimeter[i-1][0]) if (perimeter[i][0]-perimeter[i-1][0]) != 0 else 100
    xpos=[perimeter[i-1,0],perimeter[i,0]]

    ypos=[perimeter[i-1,1],perimeter[i,1]]

    if np.abs(m)>2:
        aux=not aux
    else:
        if aux==True:
            ms1.append(m)
            dx1.append(xpos)
            dy1.append(ypos)
        if aux==False:
            ms2.append(m)
            dx2.append(xpos[::-1])
            dy2.append(ypos[::-1])

dx2=np.array(dx2[::-1])
dx1=np.array(dx1)
dy2=np.array(dy2[::-1])
dy1=np.array(dy1)
ms2=np.array(ms2[::-1])
ms1=np.array(ms1)


ini=max(np.min(dx1[:,0]),np.min(dx2[:,0]))
fin=min(np.max(dx1[:,1]),np.max(dx2[:,1]))
muestra =np.linspace(ini+1,fin-1,20)

def distance (x,M,X0,Y0,m,x0,y0):
    return (M-m)*x+Y0-y0+m*x0-M*X0
thickness=np.zeros(len(muestra))

for i,el in enumerate(muestra):
    w1=[True if el>=l else False for l in dx1[:,0]]
    try: in1=w1.index(False)-1
    except ValueError: in1=-1
    w2=[True if el>=l else False for l in dx2[:,0]]
    try: in2=w2.index(False)-1
    except ValueError: in2=-1

    thickness[i]=distance(el,ms2[in2],dx2[in2,0],dy2[in2,0],ms1[in1],dx1[in1,0],dy1[in1,0])


w_M=max(thickness)/scale

w_m=min(thickness)/scale

mean=np.mean(thickness)/scale

desv=np.std(thickness)/(scale*mean)

ratio=w_m/w_M

# Para el perímetro
perimeter_list = perimeter.tolist()
perimeter_json = json.dumps(perimeter_list)

# Add time info
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")

save_var = [time,Name,Eval,Evaluator,Count,w_M,w_m,mean,desv,ratio,perimeter_json]
# ###############


# print(*save_var[:5],sep='; ',end='; ')
# for v in save_var[5:8]:
#     print(f'{v:.4f}', end='; ')
# print('[...]')

print ("The shape processing has been successfully completed")


# Save data
path = os.path.join("E:/UZqTool/uzqtool/html/DATA/",user,echo_type,"morphology.txt")
with open(path,'a') as file:
    line = ";".join(map(str, save_var))
    file.writelines(line+'\n')