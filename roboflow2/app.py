from roboflow import Roboflow
import cv2
import numpy as np
from matplotlib import pyplot as plt
import json
rf = Roboflow(api_key="your_api_key")
project = rf.workspace().project("new-final-dataset-eqnh8")
model = project.version(1).model

# infer on a local image
response_data = (model.predict("/home/plab/Desktop/roboflow/image.jpeg", confidence=40, overlap=30).json())
# 클래스 정보만 추출하여 컴마로 구분된 문자열로 저장
classes = ','.join(prediction['class'] for prediction in response_data['predictions'])
class_confidence_list = [f"[{prediction['class']}, {prediction['confidence'] * 100:.2f}%]" for prediction in response_data['predictions']]

# 컴마로 구분된 문자열로 변환
result = ','.join(class_confidence_list)

print(result)


# visualize your prediction
model.predict("/home/plab/Desktop/roboflow/image.jpeg", confidence=40, overlap=30).save("prediction.jpg")

# infer on an image hosted elsewhere
# print(model.predict("URL_OF_YOUR_IMAGE", hosted=True,
image = cv2.imread("/home/plab/Desktop/roboflow/prediction.jpg")
plt.imshow(image), plt.axis("off")
plt.show()
