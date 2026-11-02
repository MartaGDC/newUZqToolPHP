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
echo_type = sys.argv[-1]
if echo_type == 'sarcopenia':
    Name = sys.argv[1]
    _height = int(sys.argv[2])
    _width = int(sys.argv[3])
    Evaluator = sys.argv[4]
    Count = sys.argv[5]
    Eval = sys.argv[6]
    _scale = float(sys.argv[7])
    estructura = sys.argv[8]
    user = sys.argv[9]
else:
    Name = sys.argv[1]
    _height = int(sys.argv[2])
    _width = int(sys.argv[3])
    Evaluator = sys.argv[4]
    Count = sys.argv[5]
    Eval = sys.argv[6]
    _scale = float(sys.argv[7])
    user = sys.argv[8]

# Cargamos puntos del fichero
BASE_DIR = "/var/www/html" if os.path.exists("/var/www/html/Upload") else os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
polygon_path = os.path.join(BASE_DIR, "PHP", "polygon.txt")
with open(polygon_path, 'r') as file:
    points = file.readlines()[1:]
    _x = []
    _y = []
    for line in points:
        if not line.strip():
            continue
        values = line.strip().split('\t')
        _x.append(float(values[0]))
        _y.append(float(values[1]))

# Path to the image
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

# Transform the cordinates into true image
height, width = image.shape

x = [_xval * width / _width for _xval in _x]
y = [_yval * height / _height for _yval in _y]

# Set scale for each image

scale = _scale*height
#print(scale)

perimeter = np.transpose([[int(round(v)) for v in x], [int(round(v)) for v in y]])

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
    
area = PolyArea(x,y)/scale**2 if scale > 0 else 0.0
per = PolyPer(x,y)/scale if scale > 0 else 0.0
max_width = (np.max(x)-np.min(x))/scale if scale > 0 else 0.0
max_height = (np.max(y)-np.min(y))/scale if scale > 0 else 0.0
ratio = max_height/max_width if max_width != 0 else 0.0

# Para el perímetro
perimeter_list = perimeter.tolist()
perimeter_json = json.dumps(perimeter_list)


# Add time info
now = datetime.now()
time = now.strftime("%d/%m/%Y-%H:%M:%S")

if echo_type !='sarcopenia':
    save_var = [time,Name,Eval,Evaluator,Count,area,per,max_width,max_height,ratio,perimeter_json]
else:
    save_var = [time,Name,Eval,Evaluator,estructura,Count,area,per,max_width,max_height,ratio,perimeter_json]
# ###############


# print(*save_var[:5],sep='; ',end='; ')
# for v in save_var[5:8]:
#     print(f'{v:.4f}', end='; ')
# print('[...]')

print ("The shape processing has been successfully completed")


# Save data
path = os.path.join(BASE_DIR, "DATA", user, echo_type, "morphology.txt")
os.makedirs(os.path.dirname(path), exist_ok=True)
with open(path,'a') as file:
    line = ";".join(map(str, save_var))
    file.writelines(line+'\n')