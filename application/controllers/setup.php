<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup extends CI_Controller
{

  function index()
  {
		// On charge la vue qui contient le corps de la page
		$tpl['file'] = 'setup/index';

		$this->load->view('setup/template', $tpl);
  }

	// Choix de la langue
  function ajax_step2()
  {
		// Enregistrement de la langue de l'utilisateur
		$this->wizard->make_config($_POST['language']);

		sleep(1);
		die();
  }

	// Traitement du fichier 'advancedsettings.xml'
  function ajax_step3()
  {
		// Si le fichier 'advancedsettings.xml' a bien été fourni et traité, on poursuit.
		if ($this->wizard->make_database())
		{
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadAdvancedSettingsEnd();
			//-->
			</script>';
		}
		else
		{
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadErrorEnd("'.$this->lang->line('error_must_redo').'");
			//-->
			</script>';
		}

		die();
  }

	// Traitement du fichier 'sources.xml'
  function ajax_step4()
  {
		// Si le fichier 'sources.xml' a bien été fourni et traité, on poursuit.
		if ($this->wizard->make_sources())
		{
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadSourcesEnd();
			//-->
			</script>';
		}
		else
		{
			echo '<script type="text/javascript">
			<!--
				window.top.window.UploadErrorEnd("'.$this->lang->line('error_must_redo').'");
			//-->
			</script>';
		}

		die();
  }

	// Lien symbolique pour les images et fin
  function ajax_step5()
  {
		// Enregistrement du chemin des images si présent
		if ($_POST['symbolic'] != '')
		{
			// Création du lien symbolique
			if ($this->wizard->make_symbolic($_POST['symbolic']))
			{
				// Mise à jour des routes de l'application et des objets auto-charegés
				$this->wizard->make_routes();
				$this->wizard->make_autoload();

				$json = array('success' => '1');
			}
			else
			{
				$json = array('success' => '0',
											'message' => $this->lang->line('error_must_redo')
										 );
			}
		}
		else
		{
			$json = array('success' => '0',
										'message' => $this->lang->line('error_must_redo')
									 );
		}

		$json = json_encode($json);

		header('Content-type: application/json');
		echo $json;
		die();
  }

	function ajax_i_database()
	{
		// Chargement de la base de données 'video' car celle de 'xbmc' est inexistante
		$this->load->database('video');
		$this->load->dbforge();
		
		// Inclusion des informations de toutes les bases de données
		include APPPATH.'config/database.php';
		
		// Le nom de la base de données a créé est maintenant connu
		$this->dbforge->create_database($db['xbmc']['database']);
		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_configure_database');
	}

	function ajax_i_users()
	{
		$this->load->database('xbmc');
		$this->load->dbforge();

		$this->dbforge->add_field("id int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("username varchar(70) NOT NULL");
		$this->dbforge->add_field("password varchar(70) NOT NULL");
		$this->dbforge->add_field("can_change_images tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_change_infos tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_download_video tinyint(1) NOT NULL");
		$this->dbforge->add_field("can_download_music tinyint(1) NOT NULL");
		$this->dbforge->add_field("is_active tinyint(1) NOT NULL");
		$this->dbforge->add_field("is_admin tinyint(1) NOT NULL");
		$this->dbforge->add_key("id", TRUE);
		$this->dbforge->create_table('users');

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_create_users');
	}

	function ajax_i_xbmc()
	{
		$this->load->database('xbmc');

    // Chargement des modèles de la base de données 'xbmc'
    $this->load->model('xbmc/users_model');

		// Ajout de l'utilisateur xbmc et préparation de mise à jour de ses champs
		$data = array('id' => $this->users_model->add('xbmc', 'xbmc'),
								 'is_admin' => '1',
								 'can_change_infos' => '1',
								 'can_change_images' => '1',
								 'can_download_video' => '1',
								 'can_download_music' => '1',
								 );


		// Mise à jour des champs de l'utilisateur xbmc
		$this->db->update('users', $data); 

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_add_xbmc');
	}

	function ajax_i_sources()
	{
		$this->load->database('xbmc');
		$this->load->dbforge();

		$this->dbforge->add_field("id int(11) NOT NULL AUTO_INCREMENT");
		$this->dbforge->add_field("idPath int(11) NOT NULL");
		$this->dbforge->add_field("name varchar(70) NOT NULL");
		$this->dbforge->add_field("client_path varchar(512) DEFAULT NULL");
		$this->dbforge->add_field("server_path varchar(512) DEFAULT NULL");
		$this->dbforge->add_field("media_db text");
		$this->dbforge->add_field("content text");
		$this->dbforge->add_field("scraper text");
		$this->dbforge->add_field("settings text");
		$this->dbforge->add_key("id", TRUE);
		$this->dbforge->create_table('sources');

		sleep(1);
		echo '<img src="'.base_url().'assets/gui/tick.png" />'.$this->lang->line('setup_create_sources');
	}

}

/* End of file setup.php */
/* Location: ./application/controllers/setup.php */