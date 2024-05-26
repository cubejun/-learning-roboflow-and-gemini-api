from flask import Flask, request, jsonify
from flask_cors import CORS
from roboflow import Roboflow
import cv2
import numpy as np
from matplotlib import pyplot as plt
import threading
import time
import os
import pathlib
import cv2
import base64
import textwrap
import os
import PIL.Image
import mysql.connector
from gtts import gTTS
import requests
import json
import sys
import google.generativeai as genai
from IPython.display import display
from IPython.display import Markdown

db_connection = mysql.connector.connect(
    host="localhost",
    user="root",
    password="plab",
    database="exampledb"
)

# 커서 생성
db_cursor = db_connection.cursor()

# Or use `os.getenv('GOOGLE_API_KEY')` to fetch an environment variable.
GOOGLE_API_KEY = os.environ.get('GOOGLE_API_KEY')
genai.configure(api_key=GOOGLE_API_KEY)
def text_to_speech(text):
    tts = gTTS(text=text, lang='ko', tld='co.kr', slow=False)
    tts.save("output.mp3")
    #os.system("mpg321 output.mp3")

# MySQL에 데이터 삽입 함수
def insert_data_to_db(image_data1, image_data2, result, diagnosis1, diagnosis2, audio_data):
    sql = "INSERT INTO dental (image_data1, image_data2, result, diagnosis1, diagnosis2, audio_data) VALUES (%s, %s, %s, %s, %s, %s)"
    val = (image_data1, image_data2, result, diagnosis1, diagnosis2, audio_data)
    db_cursor.execute(sql, val)
    db_connection.commit()

def encode_base64(image_path):
    """
    이미지 파일을 base64로 인코딩하는 함수

    :param image_path: 이미지 파일 경로
    :return: base64로 인코딩된 이미지 문자열
    """
    with open(image_path, "rb") as image_file:
        encoded_string = base64.b64encode(image_file.read()).decode('utf-8')
    
    return encoded_string
  
model2 = genai.GenerativeModel('gemini-pro-vision')
app = Flask(__name__)
CORS(app)
# /robogemini 엔드포인트
@app.route('/robogemini', methods=['POST'])
def process_image():
    rf = Roboflow(api_key="your_api_key")
    project = rf.workspace().project("new-final-dataset-eqnh8")
    model = project.version(1).model
    # 드래그 앤 드롭으로 받은 이미지 처리
    # 여기서 gemini API 및 roboflow API에 요청 보내고 결과를 받아옵니다.
    filename = '/home/plab/Desktop/roboflow/aimage.jpg'
    response0 = (model.predict("/home/plab/Desktop/roboflow/aimage.jpg", confidence=40, overlap=30).json())

    # visualize your prediction
    model.predict("/home/plab/Desktop/roboflow/aimage.jpg", confidence=10, overlap=30).save("prediction.jpg")
    classes = ','.join(prediction['class'] for prediction in response0['predictions'])
    class_confidence_list = [f"[{prediction['class']}, {prediction['confidence'] * 100:.2f}%]" for prediction in response0['predictions']]
    # 컴마로 구분된 문자열로 변환
    result = ','.join(class_confidence_list)
    print(result)



    image_path = '/home/plab/Desktop/roboflow/aimage.jpg'
    image_path2 = '/home/plab/Desktop/roboflow/prediction.jpg'
    audio_path = "/home/plab/Desktop/roboflow/output.mp3"
    print(f'{filename} 저장됨')
    img1 = PIL.Image.open('/home/plab/Desktop/roboflow/aimage.jpg')
    img2 = PIL.Image.open('/home/plab/Desktop/roboflow/prediction.jpg')
            # Base64로 이미지 인코딩
    encoded_image1 = encode_base64(image_path)        
    encoded_image2 = encode_base64(image_path2)
            
    response1 = model2.generate_content(["이 사진은 치아를 xray로 촬영한 사진이야. 사진을 보고 진단해줘", img1])
    response1.resolve()
    if(classes == ""):
	    message23 = "훈련된 모델이 아무 증상도 찾아내지 못했어. 정확하게 찾아낸건지 진단해줘."
    else:
	    message23 = ".훈련된 모델이 " + classes + "를 찾아냈어. 훈련된 모델이 증상을 찾아 바운딩 박스를 그린 사진인데 정확하게 찾아냈는지 진단해줘"
    response2 = model2.generate_content([message23, img2])
    response2.resolve()

    print(response1.text)
    print(response2.text)
            # print(encoded_image)

    text_to_speech(response1.text + response2.text)
    encoded_audio = encode_base64(audio_path)
            # MySQL에 데이터 삽입
    insert_data_to_db(encoded_image1, encoded_image2, result, response1.text, response2.text, encoded_audio)
    return jsonify({'message': 'File processed successfully'}), 200
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8765)
