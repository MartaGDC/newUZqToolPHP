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

# calculate area
def PolyArea(x_coord,y_coord):
    return 0.5*np.abs(np.dot(x_coord,np.roll(y_coord,1))-np.dot(y_coord,np.roll(x_coord,1)))

def PolyPer(x, y):
    x = np.asarray(x)
    y = np.asarray(y)
    x = np.concatenate([x, x[0:1]])
    y = np.concatenate([y, y[0:1]])
    dx = np.diff(x)
    dy = np.diff(y)
    
    distances = np.sqrt(dx*dx + dy*dy)
    perimeter = np.sum(distances)
    
    return perimeter
    
area = PolyArea(x,y)/scale**2
per = PolyPer(x,y)/scale
max_width = (np.max(x)-np.min(x))/scale
max_height = (np.max(y)-np.min(y))/scale
ratio = max_height/max_width

# Para el perímetro
perimeter_list = perimeter.tolist()
perimeter_json = json.dumps(perimeter_list)


# Add time info
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")

save_var = [time,Name,Eval,Evaluator,Count,area,per,max_width,max_height,ratio,perimeter_json]
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