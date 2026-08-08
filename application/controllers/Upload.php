<?php

class Upload extends CI_Controller
{
  private $secret = "YXBpQHdhcnJpeDpaSzExbzE1bzE1TDEycyRwMHJ0==";
  public function __construct()
  {
    parent::__construct();
    header('Access-Control-Allow-Origin:*');    
  }

  public function index()
  {
    $sc = TRUE;    
    $key = $this->input->post('secret');
    $code = $this->input->post('order');
    $role = $this->input->post('role');
    $user = $this->input->post('user');
    $owner = $this->input->post('owner');
    $ds = [];

    if(empty($owner))
    {
      $sc = FALSE;
      $this->error = "Missing required parameter";
    }

    if($sc === TRUE)
    {
      $owner_data = $this->get_owner($owner);

      if(empty($owner_data))
      {
        $sc = FALSE;
        $this->error = "unauthorized";
      }
    }

    if ($sc === TRUE && $owner_data->secret !== $this->secret)
    {
      $sc = FALSE;
      $this->error = "unauthorized";
    }

    if($sc === TRUE)
    {
      $file = $_FILES['video'];
      $path = $this->config->item('upload_path') . "{$owner}/";

      if (! empty($file))
      {
        $fileName = $file['name'];

        $config = array(
          "allowed_types" => "*",
          "upload_path" => $path,
          "file_name"  => $fileName, // name canbe change
          "max_size" => 102400, //100 MB in KB base on php.ini setting
          "overwrite" => TRUE
        );

        $this->load->library("upload", $config);

        if (! $this->upload->do_upload('video'))
        {
          $sc = FALSE;
          $this->error = $this->upload->display_errors();
        }

        if ($sc === TRUE)
        {                    
          $ds = array(
            'order_code' => $code,
            'role' => $role,
            'create_date' => now(),
            'user' => $user,
            'file_name' => $fileName,
            'file_format' => pathinfo($fileName, PATHINFO_EXTENSION)
          );

          $id = $this->Packing_video_model->get_id($code);

          if($id)
          {
            $this->db->where('id', $id)->update('order_pack_video', $ds);
          }
          else
          {
            $this->db->insert('order_pack_video', $ds);
          }          
        }
      }
      else
      {
        $sc = FALSE;
        $this->error = "Missing required parameter";
      }
    }

    $arr = array(
      'status' => $sc === TRUE ? 'success' : 'failed',
      'message' => $sc === TRUE ? 'success' : $this->error,
      'data' => $ds
    );

    echo json_encode($arr);
  }


  public function view()
  {
    $key = $this->input->get('secret');
    $code = $this->input->get('order');
    $owner = $this->input->get('owner');

    if(empty($owner))
    {
      $this->load->view('page_unauthorized');
      return;
    }

    $owner_data = $this->get_owner($owner);

    if (empty($owner_data))
    {
      $this->load->view('page_unauthorized');
      return;
    }

    if ($key !== $owner_data->secret)
    {
      $this->load->view('page_unauthorized');
      return;
    }

    $owner = $this->input->get('owner');
    $path = $this->config->item('upload_path') . "{$owner}/" . $code . '.webm';
    $file = $this->config->item('upload_file_path') . "{$owner}/" . $code . '.webm';

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

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }


  public function get_owner($owner)
  {
    $rs = $this->db->where('owner', $owner)->where('active', 1)->get('owner');

    if ($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return NULL;
  }

  public function get_id($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('order_pack_video');

    if ($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return NULL;
  }
}
