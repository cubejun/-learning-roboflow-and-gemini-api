# Roboflow API와 gemini 1.0 pro vision을 이용한 치아 xray 진단 프로그램
## 조원 : 최영준 김경섭 김락준 박상원 장호균
## -작품 개발 동기 

GPT-4o와 같은 인공지능 기술을 활용해 질병을 진단할 수 있다는 기사를 보고 X-ray를 분석해 질병을 진단하는 프로그램을 개발해보고자 했다.

## 도구 선택 
Gemini Vision Pro API와 Roboflow는 이미지 분석과 객체 인식 분야에 사용되는 도구들로 이를 활용하여 치아 X-ray 이미지를 분석하고 진단하는 프로그램을 설계하게 되었다.

## 아이디어 도출 및 논의
1. 초기 아이디어
기술 조사: GPT-4와 같은 인공지능 기술이 질병 진단에 활용된 사례를 통해 영감을 얻음.
도구 선택: 이미지 분석 및 객체 인식을 위한 Gemini Vision Pro API와 Roboflow API를 사용하기로 결정.

2. 세부 논의
기능 정의: 사용자가 웹에서 X-ray 이미지를 업로드하고, 진단 결과를 확인할 수 있는 인터페이스 구현.
데이터 흐름: 이미지를 API에 전송하고, 결과를 받아 데이터베이스에 저장하는 일련의 과정 설계.

## 작품 제작과정
1. 웹 인터페이스 설계
이미지 업로드: 사용자가 치아 X-ray 이미지를 드래그 앤 드롭으로 업로드할 수 있는 기능 구현.
전송 버튼: 업로드된 이미지를 API로 전송하기 위한 전송 버튼 추가.

2. API 이용
Gemini Vision Pro API: 이미지 분석을 통해 치아 상태를 진단하는 진료문구를 받는 기능 구현.
Roboflow API: 질병을 식별하고 바운딩 박스를 그린 이미지를 받는 기능 구현.

3. 데이터베이스 구축
MySQL 데이터베이스: 진단 결과와 이미지를 저장하기 위한 데이터베이스 테이블 설계.
데이터 저장: API로부터 받은 진단 결과와 바운딩 박스 이미지를 MySQL 데이터베이스에 저장하는 기능 구현.

4. 결과 출력
웹 출력 기능: 데이터베이스에 저장된 진단 결과와 이미지를 웹 페이지에서 확인할 수 있는 기능 구현.


사용한 로보플로 데이터셋 & api : https://universe.roboflow.com/capstone-workspace/new-final-dataset-eqnh8

![image](https://github.com/cubejun/-learning-roboflow-and-gemini-api/assets/133946040/f634e84a-e58d-46f3-909f-b9fe47476bac)


# 영상
https://github.com/cubejun/-learning-roboflow-and-gemini-api/assets/133946040/7ecdbd76-7a0a-43d3-9a29-bd5ddd773a29

