<?php

class PMSLE_Import {

	protected $args_to_import;
	public $import_messages = array();
	private $j = '0';

	/**
	 * this will take labels that will be imported to database.
	 *
	 * @param array  $args_to_import  labels to import.
	 */
	function __construct( $args_to_import ) {
		$this->args_to_import = $args_to_import;
	}

	/**
	 * this will save imported json.
	 *
	 * @param string  $json_content  imported json.
	 */
	public function json_to_db( $json_content ) {
		/* decode and put json to array */
		$imported_array_from_json = json_decode( $json_content, true );
		if ( $imported_array_from_json !== NULL ) {
			/* import labels to database */
			foreach( $imported_array_from_json as $key => $value ) {
				if( ! empty( $value ) ) {
					update_option( $key, $value );
				}
			}
		} else {
			$this->import_messages[$this->j]['message'] = __( 'Uploaded file is not valid json!', 'pmsle' );
			$this->import_messages[$this->j]['type'] = 'error';
			$this->j++;
		}
	}

	/* upload json file function */
	public function upload_json_file() {
		if( isset( $_POST['pmsle-import'] ) ) {
			if( ! empty( $_FILES['pmsle-upload']['tmp_name'] ) ) {
				$json_content = file_get_contents( $_FILES['pmsle-upload']['tmp_name'] );
				$this->json_to_db( $json_content );

				if( empty( $this->import_messages ) ) {
					$this->import_messages[$this->j]['message'] = __( 'Import successfully!', 'pmsle' ) . "</p><p>" . __( 'Page will refresh in 3 seconds...', 'pmsle' ) . '<META HTTP-EQUIV="refresh" CONTENT="3">';
					$this->import_messages[$this->j]['type'] = 'updated';
					$this->j++;
					flush_rewrite_rules( false );
				}
			} else {
				$this->import_messages[$this->j]['message'] = __( 'Please select a .json file to import!', 'pmsle' );
				$this->import_messages[$this->j]['type'] = 'error';
				$this->j++;
			}
		}
	}

	/* messages return function */
	public function get_messages() {
		return $this->import_messages;
	}
}
