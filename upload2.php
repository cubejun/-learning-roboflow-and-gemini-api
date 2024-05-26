<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>치아 X-ray 영상 진단</title>
    <style>
        /* Drop area styles */
        #drop-area {
            border: 2px dashed #ccc;
            border-radius: 20px;
            width: 300px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
            font-family: Arial, sans-serif;
            position: relative;
        }
        
        #drop-area.highlight {
            border-color: purple;
        }
        #preview {
            display: none;
            margin-top: 20px;
            max-width: 100%;
        }
        
        /* Loading overlay styles */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
            display: none; /* Initially hidden */
            z-index: 9999; /* Ensure it's on top of everything */
        }
        
        /* Loading spinner styles */
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite; /* Rotate animation */
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Other styles */
        .uploaded-image {
            width: 600px;
            height: 400px;
            margin: 10px;
            object-fit: cover;
        }
        
        #updateGallery {
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        #gallery {
            margin-top: 20px;
            list-style: none;
            padding: 0;
        }
        
        #gallery li {
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 1px solid #ccc;
            padding-bottom: 20px;
        }
        
        #gallery img {
            max-width: 100%;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
	<h1>치아 X-ray 영상 진단</h1>
    <!-- Drop area -->
    <div id="drop-area">
        <p>여기로 이미지를 드롭하거나 클릭하세요</p>
        <input type="file" id="fileElem" accept="image/*" style="display:none">
        <label class="button" for="fileElem">파일 선택</label>
        <br>
        <img id="preview" alt="이미지 미리보기">
        <button id="updateGallery">업데이트</button>
    </div>

    <!-- Loading overlay -->
    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Gallery -->
    <ul id="gallery"></ul>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
			clearPreview();
            let dropArea = document.getElementById('drop-area');
            let fileElem = document.getElementById('fileElem');
            let updateGalleryBtn = document.getElementById('updateGallery');

            // Drag and drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false);
            });

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                let dt = e.dataTransfer;
                let files = dt.files;
                handleFiles(files);
            }

            fileElem.addEventListener('change', handleFilesFromInput, false);

            function handleFilesFromInput() {
                let files = this.files;
                handleFiles(files);
            }

            function handleFiles(files) {
                [...files].forEach(file => {
                    uploadFile(file);
                    previewFile(file);
                });
            }

            function previewFile(file) {
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = function () {
                    let img = document.getElementById('preview');
                    img.src = reader.result;
                    img.style.display = 'block';
                };
            }

            function uploadFile(file) {
                let url = 'upload.php';
                let formData = new FormData();
                formData.append('image', file);

                fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('성공적으로 업로드되었습니다:', data.message);
                        processImage(); // 업로드가 성공하면 이미지 처리 시작
                        window.scrollTo(0,document.body.scrollHeight);
                    } else {
                        console.error('업로드 오류:', data.error);
                        hideLoadingMessage(); // 업로드 실패 시 로딩 메시지 숨김
                    }
                })
                .catch(() => {
                    console.error('업로드 실패');
                    hideLoadingMessage(); // 업로드 실패 시 로딩 메시지 숨김
                });
            }

            function processImage() {
                showLoadingMessage(); // 이미지 처리 시작 시 로딩 메시지 표시
                let formData = new FormData();
                formData.append('image', document.getElementById('fileElem').files[0]);

                fetch('http://192.168.0.19:8765/robogemini', {
                    method: 'POST', // POST 요청으로 수정
                    body: formData
                })
                .then(response => {
                    if (response.ok) {
                        console.log('Flask 엔드포인트 호출 성공');
                        loadImages(); // 갤러리 업데이트
                        window.scrollTo(0,document.body.scrollHeight);
                    } else {
                        console.error('Flask 엔드포인트 호출 실패');
                    }
                    hideLoadingMessage(); // 이미지 처리 완료 후 로딩 메시지 숨김
                })
                .catch(error => {
                    console.error('Flask 엔드포인트 호출 중 오류:', error);
                    hideLoadingMessage(); // 이미지
                })
                .catch(error => {
                    console.error('Flask 엔드포인트 호출 중 오류:', error);
                    hideLoadingMessage(); // 이미지 처리 오류 시 로딩 메시지 숨김
                });
                 
            }

            function loadImages() {
                fetch('fetch_images.php')
                    .then(response => response.json())
                    .then(data => {
                        let gallery = document.getElementById('gallery');
                        gallery.innerHTML = '';
                        data.images.forEach(image => {
                            let listItem = document.createElement('li');

                            let img1 = document.createElement('img');
                            img1.src = 'data:image/jpeg;base64,' + image.image_data1;
                            img1.classList.add('uploaded-image');
                            listItem.appendChild(img1);

                            let img2 = document.createElement('img');
                            img2.src = 'data:image/jpeg;base64,' + image.image_data2;
                            img2.classList.add('uploaded-image');
                            listItem.appendChild(img2);

                            let result = document.createElement('p');
                            result.textContent = 'Result: ' + image.result;
                            listItem.appendChild(result);

                            let diagnosis1 = document.createElement('p');
                            diagnosis1.textContent = 'Diagnosis 1: ' + image.diagnosis1;
                            listItem.appendChild(diagnosis1);

                            let diagnosis2 = document.createElement('p');
                            diagnosis2.textContent = 'Diagnosis 2: ' + image.diagnosis2;
                            listItem.appendChild(diagnosis2);

                            let audio = document.createElement('audio');
                            audio.controls = true;
                            let source = document.createElement('source');
                            source.src = 'data:audio/mpeg;base64,' + image.audio_data;
                            source.type = 'audio/mpeg';
                            audio.appendChild(source);
                            listItem.appendChild(audio);

                            gallery.appendChild(listItem);
                        });
                    })
                    .catch(error => console.error('이미지 로드 오류:', error));
                    
            }

            function clearPreview() {
                let img = document.getElementById('preview');
                img.src = '';
                img.style.display = 'none';
            }

            updateGalleryBtn.addEventListener('click', () => {
                let gallery = document.getElementById('gallery');
                clearPreview();
                processImage();
            });

            // 초기 로딩 메시지 숨기기
            hideLoadingMessage();

            // 이미지 로드
            loadImages();
            window.scrollTo(0,document.body.scrollHeight);
        });

        // 로딩 메시지 표시 함수
        function showLoadingMessage() {
            document.getElementById('loading-overlay').style.display = 'block';
        }

        // 로딩 메시지 숨기기 함수
        function hideLoadingMessage() {
            document.getElementById('loading-overlay').style.display = 'none';
        }
    </script>
</body>
</html>
