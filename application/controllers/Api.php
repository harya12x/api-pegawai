<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, OPTIONS");
defined('BASEPATH') or exit('No direct script access allowed');


class Api extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('M_data');
  }

  public function index()
  {
    $status = array(
      'status' => 'Ok'
    );
    echo json_encode($status);
  }

  public function absenapi()
  {
    $query = $this->M_data->absenapi('absen')->result();
    echo json_encode($query);
  }

  public function cutiapi()
  {
    $query = $this->M_data->datacutiapi('cuti')->result();
    echo json_encode($query);
  }


  //API untuk Ionic
  public function Login()
  {
    $email = urldecode($this->uri->segment(3));
    $password = md5(urldecode($this->uri->segment(4)));

    $query = $this->M_data->CheckLogin("user", $email, $password);
    if ($query) {
      redirect("index.php/Api");
    } else {
      $status = [
        "status" => "Error",
      ];
      echo json_encode($status);
    }
  }
  public function GetCountCuti()
  {
    // $data = [
    //     'nip' =>  urldecode($this->uri->segment(3))
    // ];
    $nip = urldecode($this->uri->segment(3));
    $query = [
      "a" => $this->M_data->count_sakit($nip)->result(),
      "b" => $this->M_data->count_izin($nip)->result(),
      "c" => $this->M_data->count_cuti_($nip)->result(),
    ];
    echo json_encode($query);
  }

  public function GetDetailCuti()
  {
    // $data = [
    //     'nip' =>  urldecode($this->uri->segment(3))
    // ];
    $nip = urldecode($this->uri->segment(3));
    $jenis_cuti = urldecode($this->uri->segment(4));
    $query = $this->db
      ->get_where("cuti", ["nip" => $nip, "jenis_cuti" => $jenis_cuti])
      ->result();
    echo json_encode($query);
  }

  public function GetAwalMasuk()
  {
    // $data = [
    //     'nip' =>  urldecode($this->uri->segment(3))
    // ];
    $nip = urldecode($this->uri->segment(3));
    $query = $this->M_data->pegawaiid($nip)->result();
    echo json_encode($query);
  }

  public function GetName()
  {
    // $data = [
    //     'nip' =>  urldecode($this->uri->segment(3))
    // ];
    $nip = urldecode($this->uri->segment(3));
    $query = $this->M_data->GetData("user", "nip", $nip)->result();
    echo json_encode($query);
  }

  public function GetCuti()
  {
    // $data = [
    //     'nip' =>  urldecode($this->uri->segment(3))
    // ];
    $nip = urldecode($this->uri->segment(3));
    $query =
      [
        'a' => $this->M_data->count_sakit($nip)->result(),
        'b' => $this->M_data->count_izin($nip)->result(),
        'c' => $this->M_data->count_cuti_($nip)->result()
      ];
    echo json_encode($query);
  }

  public function CekPresensi()
  {
    $nip = urldecode($this->uri->segment(3));
    $year = urldecode($this->uri->segment(4));
    $month = urldecode($this->uri->segment(5));
    $day = urldecode($this->uri->segment(6));

    $query = $this->M_data->absendaily($nip, $year, $month, $day)->result();
    echo json_encode($query);
  }

  public function InputPresensi()
  {
    $data = [
      "nip" => urldecode($this->uri->segment(3)),
      "keterangan" => urldecode($this->uri->segment(4)),
    ];
    $query = $this->M_data->insertpresensi("absen", $data);
    if ($query) {
      redirect("index.php/Api");
    } else {
      $status = [
        "status" => "Error",
      ];
      echo json_encode($status);
    }
  }

  public function upload()
  {
    $config["upload_path"] = "./bukti/";
    $config["allowed_types"] = "jpg|png|jpeg";
    $config["overwrite"] = true;
    $config["max_size"] = 5000;
    $tgl1 = date("Y-m-d", strtotime(urldecode($this->uri->segment(6))));
    $data = [
      "nip" => urldecode($this->uri->segment(3)),
      "jenis_cuti" => urldecode($this->uri->segment(4)),
      "alasan" => urldecode($this->uri->segment(5)),
      "status" => "diajukan",
      "selesai_pengajuan" => $tgl1,
    ];
    $this->load->library("upload", $config);
    if ($data["jenis_cuti"] == "sakit") {
      $uploadfile = $this->upload->do_upload("file");
      if (!$uploadfile) {
        $error = ["error" => $this->upload->display_errors()];
        echo json_encode($error);
      } else {
        $img = $this->upload->data();
        $data["bukti"] = $img["file_name"];

        header("Content-type: application/json");
        $this->db->insert("cuti", $data);
        $status = ["message" => "success"];
        echo json_encode($status);
      }
    } else {
      header("Content-type: application/json");
      $data["bukti"] = null;
      $this->db->insert("cuti", $data);
      $status = ["message" => "success"];
      echo json_encode($status);
    }
  }


  public function penggajianJoin()
  {
    $query = $this->M_data->penggajianJoin("pegawai");
    if ($query) {
    }
    echo json_encode($query);
  }

  public function datacutiapi()
  {
    $query = $this->M_data->datacutiapi("cuti");
    echo json_encode($query);
  }
}
