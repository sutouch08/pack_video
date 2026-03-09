<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8" />
  <title>Video Player ด้วย Video.js</title>

  <!-- Video.js CSS (CDN) -->
  <link href="<?php echo base_url(); ?>assets/css/video-js.css" rel="stylesheet" />

  <style>
    /* จัดหน้าให้วิดีโออยู่กลางจอ */
    body {
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background: #111;
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .player-wrapper {
      width: 80%;
      max-width: 900px;
    }

    h1 {
      text-align: center;
      margin-bottom: 16px;
      font-size: 1.6rem;
    }

    /* ปรับสไตล์ของ video.js เพิ่มเล็กน้อย */
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
        data-setup="{}" style="aspect-ratio: 4/3;">
        <!-- เปลี่ยน src เป็นไฟล์วิดีโอของคุณเองได้ -->
        <source src="<?php echo base_url().$path; ?>" type="video/webm" />      
        <p class="vjs-no-js">
          กรุณาเปิดใช้งาน JavaScript เพื่อดูวิดีโอนี้
        </p>
      </video>      
    </div>

    <!-- Video.js JS (CDN) -->
    <script src="<?php echo base_url(); ?>assets/js/video.min.js"></script>

    <script>            
      const player = videojs('my-video', {
        playbackRates: [0.5, 1, 1.5, 2],
        controls: true,
        fluid: true
      });

      // ตัวอย่าง event
      player.on('play', () => {
        if(player.networkState === 3) {
          console.log('เริ่มเล่นวิดีโอ');          
        }
      });
    </script>
  <?php endif; ?>
</body>

</html>