<?php
class Auto_delete extends CI_Controller
{  
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url() . 'auto/auto_delete';    
  }

  public function index($owner = NULL)
  {
    $enabled = is_true(getConfig('AUTO_DELETE'));

    if($enabled)
    {      
      $limit = intval(getConfig('AUTO_DELETE_LIMIT'));
      $keeping_days = intval(getConfig('AUTO_DELETE_KEEPING_DAYS'));
      $date = date('Y-m-d 00:00:00', strtotime("-{$keeping_days} days"));

      $list = $this->get_delete_list($date, $limit);

      if(!empty($list))
      {
        $ids = [];
        $total_deleted = 0;
        $path = $this->config->item('upload_file_path') . "{$owner}/";

        foreach($list as $rs)
        {
          $file = $path . $rs->file_name;

          if($this->delete_file($file))
          {
            $ids[] = $rs->id;
            $total_deleted++;
          }
        }

        if(!empty($ids))
        {
          $this->deleteRows($ids);
        }

        $logs = array(
          'start_at' => now(),
          'finish_at' => now(),
          'total_deleted' => $total_deleted,
          'msg' => 'Auto delete completed'
        );

        $this->add_logs($logs);
      }
    }
    else 
    {
      $logs = array(
        'start_at' => now(),
        'finish_at' => now(),
        'total_deleted' => 0,
        'msg' => 'Auto delete is disabled'
      );

      $this->add_logs($logs);
    }
  }

  public function get_delete_list($date, $limit = 1000)
  {
    $rs = $this->db->select('id, order_code AS code, file_name')->where('create_date <', $date)->limit($limit)->get('order_pack_video');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

  public function deleteRows($ids)
  {
    return $this->db->where_in('id', $ids)->delete('order_pack_video');
  }

  public function add_logs(array $ds = array())
  {
    return $this->db->insert('delete_logs', $ds);
  }

  public function delete_file($file)
  {
    if(file_exists($file))
    {
      return unlink($file);
    }

    return TRUE;
  }

} //--- end class
