<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class approval_model extends CI_Model {

    protected $_table;
    protected $_countAll;
    protected $_tempResult;

    public function __construct() {
        parent::__construct();
        $this->config->load('database_tables', TRUE);
        $this->_table = $this->config->item('database_tables');
    }

    public function getJurnal($idproyek, $limit, $offset, $sidx, $sord, $cari, $search = "false") {

        if (!is_array($cari))
            $cari = array();

        $this->db->start_cache();

        if (!empty($cari) and is_array($cari)) {
            foreach ($cari as $row) {
                if (!empty($row['cols']) AND !empty($row['ops'])) {
                    $fields = explode(":", $row['cols']);
                    switch ($row['ops']) {
                        case "=":
                            $this->db->where($fields[1], strtolower($row['vals']));
                            break;
                        case "!=":
                            $this->db->where($fields[1] . " !=", strtolower($row['vals']));
                            break;
                        case "like":
                            $this->db->like($fields[1], strtolower($row['vals']), 'both');
                            break;
                        case "not like":
                            $this->db->not_like($fields[1], strtolower($row['vals']));
                            break;
                        case ">":
                            $this->db->where($fields[1] . " >", strtolower($row['vals']));
                            break;
                        case "<":
                            $this->db->where($fields[1] . " <", strtolower($row['vals']));
                            break;
                        case ">=":
                            $this->db->where($fields[1] . " >=", strtolower($row['vals']));
                            break;
                        case "<=":
                            $this->db->where($fields[1] . " <=", strtolower($row['vals']));
                            break;
                    }
                }
            }
        }

        $this->db->from('listjurnal_v');
        $this->db->where('id_proyek', $idproyek);
        $this->_countAll = $this->db->count_all_results();
        $this->db->select('kdperkiraan, coa, rekanan, id_proyek, kode_proyek, nama_proyek, id_tempjurnal, tanggal_format, nobukti, no_dokumen, dperkir_id, keterangan, volume, dk, debit, kredit, isapprove, gid');
        $this->db->order_by($sidx, $sord);
        $this->db->order_by('dk', 'asc');
        $this->db->limit($limit, $offset);
        $query = $this->db->get('listjurnal_v');
        $this->db->flush_cache();

        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User menampilkan semua jurnal di modul Approval',
											'log_params' => json_encode($cari)
										)
									  );
        $temp_result = array();
        foreach ($query->result_array() as $row) {
            $temp_result[$row["nobukti"]]["tanggal"] = $row["tanggal_format"];
            $temp_result[$row["nobukti"]]["nobukti"] = $row["nobukti"];
            $temp_result[$row["nobukti"]]["no_dokumen"] = $row["no_dokumen"];
            $temp_result[$row["nobukti"]]["id_proyek"] = $row["nama_proyek"];
            $temp_result[$row["nobukti"]]["gid"] = $row["gid"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["id_tempjurnal"] = $row["id_tempjurnal"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["id_proyek"] = $row["nama_proyek"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["dk"] = $row["dk"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["kdperkiraan"] = $row["kdperkiraan"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["kdnasabah"] = $row["rekanan"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["keterangan"] = $row["keterangan"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["volume"] = $row["volume"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["debet"] = $row["debit"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["kredit"] = $row["kredit"];
            $temp_result[$row["nobukti"]]["detail"][$row["id_tempjurnal"]]["is_approved"] = $row["isapprove"];
        }
        return $temp_result;
    }

    public function countAll() {
        return $this->_countAll;
    }

    public function JurnalToJson($data = array()) {
		//die(print_r($data));
        if (!empty($data) and is_array($data) and count($data) <> 0) {
            $no = 1;
            $countDK = 0;
            $temp_result = array();
            foreach ($data as $key => $value) {
                $check = 0;
				$countDK = $this->countDK($value["nobukti"]);
                foreach ($value["detail"] as $key => $value2) {
                    if ($check < 1 and $check == 0) {
                        $edit = $value2["is_approved"] == "FALSE" ? '<a class="link_edit" href="#" onclick="edit_jurnal('.$value2["id_tempjurnal"].',\'' . $value["nobukti"] . '\','.$countDK.');"><img src="' . base_url() . 'media/edit.png" /></a>' : '' ;
						$checkbox = $value2["is_approved"] == "FALSE" && $countDK > 1? "<input type=\"checkbox\" value=\"" . $value["nobukti"] . "\" name=\"jq_checkbox_added[]\" class=\"jq_checkbox_added\" />" : "" ;
						$temp_result[] = array(
                            "no" => $no,
                            "check" => $checkbox,
                            "flag" => $edit,
                            "tanggal" => $value["tanggal"],
                            "nomor_bukti" => $value["nobukti"],
                            "nomor_dokumen" => $value["no_dokumen"],
                            "kode_proyek" => $value["id_proyek"],
                            "coa" => $value2["kdperkiraan"],
                            "rekanan" => $value2["kdnasabah"],
                            "keterangan" => $value2["keterangan"],
                            "volume" => $value2["volume"],
                            "debet" => myFormatMoney($value2["debet"]),
                            "kredit" => myFormatMoney($value2["kredit"]),
                            "is_approved" => $value2["is_approved"]
                        );
                    } else {
                        $temp_result[] = array(
                            "no" => "",
                            "check" => "",
                            "flag" => "",
                            "tanggal" => "",
                            "nomor_bukti" => "",
                            "nomor_dokumen" => "",
                            "kode_proyek" => "",
                            "coa" => $value2["kdperkiraan"],
                            "rekanan" => $value2["kdnasabah"],
                            "keterangan" => $value2["keterangan"],
                            "volume" => $value2["volume"],
                            "debet" => myFormatMoney($value2["debet"]),
                            "kredit" => myFormatMoney($value2["kredit"]),
                            "is_approved" => $value2["is_approved"]
                        );
                    }
                    $check++;
                }
                $no++;
            }
            return $temp_result;
        }
    }

    public function ApprovJurnal($kode_proyek, $user_id, $data = array()) {
        if (!is_array($data))
            $data = array();

        $strArray = "ARRAY[";
        foreach ($data as $key => $value) {
            if ($key >= end(array_keys($data))) {
                $strArray .= "'" . $value . "'";
            } else {
                $strArray .= "'" . $value . "',";
            }
        }
        $strArray .= "]";

        $query = $this->db->query("select * from  tempjurnal_approve(" . $kode_proyek . "," . $user_id . "," . $strArray . ")");
        $temp_result = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $temp_result[] = array(
                    'no_dokumen' => $row["approv_jurnal_no_dokumen"],
                    'nobukti' => $row["approv_jurnal_nobukti"],
                    'status' => $row["approv_jurnal_status"],
                    'keterangan' => $row["approv_jurnal_keterangan"],
                );
                $this->edit_tempjurnal(array("is_delete" => 'f'),"RK".$row["approv_jurnal_nobukti"]);
                $this->edit_tempjurnal(array("is_delete" => 'f'),"PU".$row["approv_jurnal_nobukti"]);
                $this->edit_tempjurnal(array("is_delete" => 'f'),"HT".$row["approv_jurnal_nobukti"]);
            }
            return $temp_result;
        }
        $this->dataset_db->insert_logs(
										array(
											'log_username' => $this->session->userdata('ba_username'),
											'log_node' => $_SERVER['REMOTE_ADDR'],
											'log_description' => 'User melakukan approval jurnal',
											'log_params' => json_encode($temp_result)
										)
									  );
        return false;
    }

    public function cek_gid($id_proyek, $gid) {
        $this->db->select("is_approved,gid,tempjurnal_jenisjurnal_id");
        $this->db->from("tbl_tempjurnal");
        $this->db->where("id_proyek", $id_proyek);
        $this->db->where("((NOT is_delete) AND (gid = " . $gid . "))");
        $this->db->group_by(array("is_approved", "gid", "tempjurnal_jenisjurnal_id"));
        $query = $this->db->get();

        $temp_result = array();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            $temp_result = array(
                "is_approved" => $row["is_approved"],
                "gid" => $row["gid"],
                "tempjurnal_jenisjurnal_id" => $row["tempjurnal_jenisjurnal_id"],
            );
            return $temp_result;
        } else {
            return false;
        }
    }

    public function get_nobukti($nomor_bukti) {
        $this->db->select("*");
        $this->db->from("listjurnal_v");
        $this->db->where("nobukti", $nomor_bukti);
        $this->db->order_by("gid", "asc");
        $this->db->order_by("dk", "asc");
        $this->db->order_by("id_tempjurnal", "asc");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $temp_result = array();
            foreach ($query->result_array() as $row) {
				$jenis = explode('/',$row["nobukti"]);
                $temp_result["jurnal"]["tanggal"] = $row["tanggal_format"];
                $temp_result["jurnal"]["no_dokumen"] = $row["no_dokumen"];
				$temp_result["jurnal"]["jenis_transaksi"] = $jenis[2];
				$temp_result["jurnal"]["jenis_jurnal"] = $row["tempjurnal_jenisjurnal_id"];
                $temp_result["jurnal"]["nobukti"] = $row["nobukti"];
                $temp_result["jurnal"]["id_proyek"] = $row["id_proyek"];
                $temp_result["jurnal"]["is_approved"] = $row["isapprove"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["gid"] = $row["gid"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["keterangan"] = $row["keterangan"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["nilai"] = $row["nilai"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["id_tempjurnal"] = $row["id_tempjurnal"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["dperkir_id"] = $row["dperkir_id"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["kdperkiraan"] = $row["kdperkiraan"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["kdnasabah"] = $row["kdnasabah"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["kdsbdaya"] = $row["kdsbdaya"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["is_rekanan"] = $row["is_rekanan"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["is_sbdaya"] = $row["is_sbdaya"];

                if (strtolower($row["is_rekanan"]) == "t") {
                    $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["bukubantu"] = $row["kdnasabah"];
                } elseif (strtolower($row["is_sbdaya"]) == "t") {
                    $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["bukubantu"] = $row["kdsbdaya"];
                } else {
                    $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["bukubantu"] = "";
                }

                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["dk"] = $row["dk"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["volume"] = $row["volume"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["debet"] = $row["debit"];
                $temp_result["jurnal"]["detail"][$row["gid"]]["detail"][$row["dk"]]["kredit"] = $row["kredit"];
            }

            $this->_tempResult = $temp_result;
        } else {
            return array();
        }
    }

    public function getArrayNobukti() {
        $temp_result = array();
        $x = 1;
        foreach ($this->_tempResult as $key1 => $value1) {
            $temp_result["jurnal"]["tanggal"] = $value1["tanggal"];
            $temp_result["jurnal"]["no_dokumen"] = $value1["no_dokumen"];
            $temp_result["jurnal"]["jenis_jurnal"] = $value1["jenis_jurnal"];
            $temp_result["jurnal"]["jenis_transaksi"] = $value1["jenis_transaksi"];
            $temp_result["jurnal"]["nobukti"] = $value1["nobukti"];
            $temp_result["jurnal"]["id_proyek"] = $value1["id_proyek"];
            $temp_result["jurnal"]["is_approved"] = $value1["is_approved"];

            foreach ($value1["detail"] as $key2 => $value2) {
                $temp_result["jurnal"]["detail"][$x]["gid"] = $value2["gid"];
                $temp_result["jurnal"]["detail"][$x]["keterangan"] = $value2["keterangan"];
                $temp_result["jurnal"]["detail"][$x]["nilai"] = $value2["nilai"];

                foreach ($value2["detail"] as $key3 => $value3) {
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["id_tempjurnal"] = $value3["id_tempjurnal"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["dperkir_id"] = $value3["dperkir_id"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["kdperkiraan"] = $value3["kdperkiraan"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["kdnasabah"] = $value3["kdnasabah"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["kdsbdaya"] = $value3["kdsbdaya"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["is_rekanan"] = $value3["is_rekanan"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["is_sbdaya"] = $value3["is_sbdaya"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["bukubantu"] = $value3["bukubantu"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["dk"] = $value3["dk"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["volume"] = $value3["volume"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["debet"] = $value3["debet"];
                    $temp_result["jurnal"]["detail"][$x]["detail"][$key3]["kredit"] = $value3["kredit"];
                }
                $x++;
            }
        }
        return $temp_result;
    }
    
    public function edit_tempjurnal($data, $nobukti){
		$this->db->where('nobukti', $nobukti);
        $this->db->update('tbl_tempjurnal', $data);
        return $nobukti;
	}
	
	public function getNobukti($id) {
        $this->db->select('nobukti');
        $this->db->from('tbl_tempjurnal');
        $this->db->where('id_tempjurnal', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['nobukti'];
        } else {
            return false;
        }
    }
    
    public function countDK($nobukti) {
		$this->db->start_cache();
        $this->db->select('dk');
        $this->db->from('tbl_tempjurnal');
        $this->db->where('nobukti', $nobukti);
        $this->db->group_by('dk');
        $query = $this->db->get();
        $this->db->flush_cache();
        return $query->num_rows();
    }
	
}
