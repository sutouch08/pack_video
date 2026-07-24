<?php

class Qc extends CI_Controller
{
  public function __construct()
  {
    parent::__construct();
    header('Access-Control-Allow-Origin:*');
  }

  public function index()
  {
    $sc = TRUE;
    $secret = "YXBpQHdhcnJpeDpaSzExbzE1bzE1TDEycyRwMHJ0==";
    $key = $this->input->post('secret');
    $code = $this->input->post('order');
    $role = $this->input->post('role');
    $user = $this->input->post('user');

    if($key !== $secret)
    {
      $sc = FALSE;
      $this->error = "unauthorized";
    }
    else
    {
      $file = $_FILES['video'];
      $path = $this->config->item('upload_path').'warrix/';

      if( ! empty($file))
      {
        $fileName = $file['name'];

        $config = array(
          "allowed_types" => "*",
          "upload_path" => $path,
          "file_name"	=> $fileName, // name canbe change
          "max_size" => 102400, //100 MB in KB base on php.ini setting
          "overwrite" => TRUE
        );

        $this->load->library("upload", $config);

        if( ! $this->upload->do_upload('video'))
        {
          $sc = FALSE;
          $this->error = $this->upload->display_errors();
        }

        if($sc === TRUE)
        {
          $arr = array(
            'order_code' => $code,
            'role' => $role,
            'user' => $user,
            'file_name' => $fileName,
            'file_format' => pathinfo($fileName, PATHINFO_EXTENSION)
          );

          $this->db->insert('order_pack_video', $arr);
        }
      }
      else
      {
        $sc = FALSE;
        set_error('required');
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'secret' => $key
    );

    echo json_encode($arr);
  }


  public function view($code)
  {
    $format = getConfig('VIDEO_FORMAT');
    $format = empty($format) ? 'webm' : $format;
    $path = $this->config->item('upload_path').'warrix/'.$code.'.'.$format;
    $file = $this->config->item('upload_file_path').'warrix/'.$code.'.'.$format;

    $ds = [
      'code' => $code,
      'path' => $path,
      'video_data' => file_exists($file) ? $this->get_video_data($code) : NULL
    ];
    
    $this->load->view('video', $ds);
  }


  public function get_video_data($code)
  {
    $rs = $this->db->where('order_code', $code)->order_by('create_date', 'DESC')->limit(1)->get('order_pack_video');
    
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }
    
    return NULL;
  }
}
?>
