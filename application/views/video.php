<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8" />
  <title><?php echo $code; ?></title>

  <!-- Video.js CSS (CDN) -->
  <link href="<?php echo base_url(); ?>assets/css/video-js.css" rel="stylesheet" />

  <style>
    /* จัดหน้าให้วิดีโออยู่กลางจอ */
    body {
      margin: 0;
      padding: 20px;
      font-family: sans-serif;
      background: #111;
      color: #fff;     
    }

    .player-wrapper {
      max-width: 800px;
      margin: auto;
      text-align: center;
    }

    h1 {
      text-align: center;
      margin-bottom: 16px;
      font-size: 1.6rem;
    }

    .controls {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 10px;
      }

      button {
        padding: 10px 16px;
        font-size: 16px;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        background: #03a9f4;
      }  
   
    .video-js {
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);      
    }
   
    .hide {
      display: none;
    }
  </style>
</head>

<body>
  <?php if( empty($video_data)) : ?>
    Video does not exists OR video has been removed.
  <?php else : ?>      
    <div class="player-wrapper">      
      <h5 style="text-align:center;"><?php echo $code; ?> &nbsp;&nbsp; Create at  <?php echo thai_date($video_data->create_date, TRUE); ?> &nbsp;&nbsp; BY  <?php echo $video_data->user; ?></h5>

      <!-- วิดีโอ -->
      <video
        id="my-video"
        class="video-js vjs-default-skin"
        controls
        preload="auto"
        width="640"
        height="360"        
        data-setup="{}" style="aspect-ratio: 16/9;">
        <!-- เปลี่ยน src เป็นไฟล์วิดีโอของคุณเองได้ -->
        <source src="<?php echo base_url().$path; ?>" type="video/webm" />      
        <p class="vjs-no-js">
          กรุณาเปิดใช้งาน JavaScript เพื่อดูวิดีโอนี้
        </p>
      </video> 

      <div class="controls">
        <button class="btn-rotate" id="rotateBtn">Rotate 180°</button>
        <button class="btn-mirror" id="mirrorBtn">Mirror</button>
        <button class="btn-reset" id="resetBtn">Reset</button>
      </div>     
    </div>

    <!-- Video.js JS (CDN) -->
    <script src="<?php echo base_url(); ?>assets/js/video.min.js"></script>

    <script>            
      const player = videojs('my-video', {
        playbackRates: [0.5, 1, 1.5, 2],
        controls: true,
        fluid: true
      });

      let rotated = false;
      let mirrored = false;

      const videoEl = document.querySelector('#my-video video');

      const applyTransform = () => {
        const rotateValue = rotated ? 'rotate(180deg)' : 'rotate(0deg)';
        const mirrorValue = mirrored ? 'scaleX(-1)' : 'scaleX(1)';
        videoEl.style.transform = `${rotateValue} ${mirrorValue}`;
      };

      document.getElementById('rotateBtn').addEventListener('click', () => {
        rotated = !rotated;
        applyTransform();
      });

      document.getElementById('mirrorBtn').addEventListener('click', () => {
        mirrored = !mirrored;
        applyTransform();
      });

      document.getElementById('resetBtn').addEventListener('click', () => {
        rotated = false;
        mirrored = false;
        applyTransform();
      });

      // // ตัวอย่าง event
      // player.on('play', () => {
      //   if(player.networkState === 3) {
      //     console.log('เริ่มเล่นวิดีโอ');          
      //   }
      // });
    </script>
  <?php endif; ?>
</body>

</html>