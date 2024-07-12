<!-- resources/views/card.blade.php -->

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kart</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 60px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Modal'ı açmak için bir düğme -->
    <button id="openModal">Kart</button>

    <!-- Modal Yapısı -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Veri kaynağı</h2>
            <form id="deviceForm">
                <label for="device">Cihaz</label>
                <input type="text" id="device" name="device" required>
                <label for="label">Etiket</label>
                <input type="text" id="label" name="label" required>
                <!-- Diğer giriş alanları buraya eklenebilir -->
                <button type="button" id="saveData">Kaydet</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modal = document.getElementById("myModal");
            var btn = document.getElementById("openModal");
            var span = document.getElementsByClassName("close")[0];

            btn.onclick = function() {
                modal.style.display = "block";
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            document.getElementById("saveData").onclick = function() {
                var device = document.getElementById("device").value;
                var label = document.getElementById("label").value;

                var xhr = new XMLHttpRequest();
                xhr.open("POST", "/save-device-data", true);
                xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.responseText);
                        // Başarılı yanıt sonrası yapılacak işlemler
                    }
                };
                var data = JSON.stringify({ "device": device, "label": label });
                xhr.send(data);
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
