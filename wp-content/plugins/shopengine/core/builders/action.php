<?php

namespace ShopEngine\Core\Builders;

defined('ABSPATH') || exit;

use ShopEngine\Core\Template_Cpt;
use ShopEngine\Traits\Singleton;

/**
 * Action Class.
 * for post insert, update and get data.
 *
 * @since 1.0.0
 */
class Action {

	use Singleton;

	const PK__SHOPENGINE_TEMPLATE = 'shopengine_template__post_meta';

	const EDIT_WITH_GUTENBERG = 'gutenberg';
	const EDIT_WITH_ELEMEBTOR = 'elementor';

	public $key_form_settings;
	private $post_type;

	private $fields;
	private $form_id;
	private $form_setting;
	private $title;
	private $response = [];
    private static $edit_with = '' ;

	/**
	 * Public function __construct.
	 * call function for all
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->post_type = Template_Cpt::TYPE;

		$this->key_form_settings = self::PK__SHOPENGINE_TEMPLATE;

		$this->response = [
			'saved'  => false,
			'status' => esc_html__("Something went wrong.", 'shopengine'),
			'data'   => [],
		];
	}


	/**
	 * Public function store.
	 * store data for post
	 *
	 * @since 1.0.0
	 */
	public function store($form_id, $form_setting) {

		$this->fields = $this->get_fields();
		$this->set_values($form_setting);
		$this->form_id = $form_id;

		if($this->form_id == 0) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules();
			$wp_rewrite->init();

			$this->insert();
		} else {
			$this->update();
		}

		return $this->response;
	}


	public function insert() {

		$this->title = ($this->form_setting['form_title'] != '') ? $this->form_setting['form_title'] : 'New Template # ' . time();

		$defaults = [
			'post_title'  => $this->title,
			'post_status' => 'publish',
			'post_type'   => $this->post_type,
		];

		$this->form_id = wp_insert_post($defaults);

		//set default meta
		$edit_with = isset($this->form_setting['edit_with_option']) ? $this->form_setting['edit_with_option'] : self::EDIT_WITH_GUTENBERG;
		$default   = isset($this->form_setting['set_default']) ? $this->form_setting['set_default'] : 'No';
		$type      = isset($this->form_setting['form_type']) ? $this->form_setting['form_type'] : 'single';

		// check options key value
		$key_type = self::PK__SHOPENGINE_TEMPLATE . '__' . $type;

		if($default == 'Yes') {
			update_option($key_type, $this->form_id);
		}


		update_post_meta($this->form_id, self::PK__SHOPENGINE_TEMPLATE, $this->form_setting);
		update_post_meta($this->form_id, self::get_meta_key_for_edit_with(), $edit_with);
		update_post_meta($this->form_id, self::get_meta_key_for_type(), $type);
		update_post_meta($this->form_id, 'shopengine_template_uuid', time() . '-' . $this->form_id);

		if($edit_with === self::EDIT_WITH_ELEMEBTOR) {


			// auto elementor canvas style
			if(in_array($type, ['quick_checkout', 'quick_view'])) {
				update_post_meta($this->form_id, '_wp_page_template', 'elementor_canvas');
			} else {
				update_post_meta($this->form_id, '_wp_page_template', 'elementor_header_footer');
			}
			update_post_meta($this->form_id, '_elementor_edit_mode', 'builder');
			update_post_meta($this->form_id, '_elementor_version', '3.4.6');
		}


		if(!empty($this->form_setting['sample_design'])) {
			$design_data = \ShopEngine\Core\Sample_Designs\Base::instance()->get_design_data($this->form_setting['sample_design']);
			if(!is_null($design_data)) {
				/**
				 *  for unicode character support
				 */
				$design_data = wp_slash( wp_json_encode( $design_data ) );

				update_post_meta($this->form_id, '_elementor_data', $design_data);
			}
		}

		$this->response['saved']  = true;
		$this->response['status'] = esc_html__('Template settings inserted', 'shopengine');

		$this->response['data']['id']    = $this->form_id;
		$this->response['data']['title'] = $this->title;
		$this->response['data']['type']  = $this->post_type;
	}

	public function update() {

		$this->title = ($this->form_setting['form_title'] != '') ? $this->form_setting['form_title'] : 'New Template # ' . time();

		if(isset($this->form_setting['form_title'])) {
			$update_post = [
				'ID'          => $this->form_id,
				'post_title'  => $this->title,
				'post_status' => 'publish',
			];

			wp_update_post($update_post);
		}

		/**
		 * This need to be saved into meta too
		 *
		 */
		$default = isset($this->form_setting['set_default']) ? $this->form_setting['set_default'] : 'No';
		$this->form_setting['set_default'] = $default;

		// save custom meta data
		update_post_meta($this->form_id, $this->key_form_settings, $this->form_setting);

		$type    = isset($this->form_setting['form_type']) ? $this->form_setting['form_type'] : 'single';

		update_post_meta($this->form_id, $this->key_form_settings . '__type', $type);

		// check options key value
		$keyType = $this->key_form_settings . '__' . $type;

		if($default == 'Yes') {
			update_option($keyType, $this->form_id);
		}

		if($default == 'No') {
			update_option($keyType, 0);
		}

		$this->response['saved']  = true;
		$this->response['status'] = esc_html__('Template settings updated', 'shopengine');

		$this->response['data']['id']    = $this->form_id;
		$this->response['data']['title'] = $this->title;
		$this->response['data']['type']  = $this->post_type;
	}


	/**
	 *
	 * @return array
	 */
	public function get_fields() {

		return [

			'form_title'    => [
				'name' => 'form_title',
			],
			'form_type'     => [
				'name' => 'form_type',
			],
			'set_default'   => [
				'name' => 'set_default',
			],
			'edit_with_option'     => [
				'name' => 'edit_with_option',
			],
			'sample_design' => [
				'name' => 'sample_design',
			],
		];
	}


	/**
	 *
	 * @param $form_setting
	 * @param null $fields
	 */
	public function set_values($form_setting, $fields = null) {

		if($fields == null) {
			$fields = $this->fields;
		}

		foreach($form_setting as $key => $value) {

			if(isset($fields[$key])) {
				$this->form_setting[$key] = $value;
			}
		}
	}


	/**
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function get_all_data($post_id) {

		$post = get_post($post_id);
		$data = get_post_meta($post->ID, $this->key_form_settings, true);
		$type = isset($data['form_type']) ? $data['form_type'] : 'single';

		$data['form_title']  = get_the_title($post_id);
		$data['set_default'] = 'No';
		$data['edit_with_option']   = get_post_meta($post->ID, self::get_meta_key_for_edit_with(), true);

		if(Templates::get_registered_template_id($type) == $post->ID) {
			$data['set_default'] = 'Yes';
		}

		return $data;
	}


	public static function get_meta_key_for_type() {

		return self::PK__SHOPENGINE_TEMPLATE . '__type';
	}

	public static function get_meta_key_for_edit_with() {

		return self::PK__SHOPENGINE_TEMPLATE . '__edit_with';
	}


	public static function edit_with( $template_id ) {

		if ( static::$edit_with ) {
			return static::$edit_with;
		}

		$edit_with         = get_post_meta( $template_id, Action::get_meta_key_for_edit_with(), true );
		static::$edit_with = empty( $edit_with ) ? Action::EDIT_WITH_ELEMEBTOR : $edit_with;

		return static::$edit_with;
	}

	public static function is_edit_with_gutenberg($pid) {

		$edit_with = get_post_meta($pid, Action::get_meta_key_for_edit_with(), true);
		$edit_with = empty($edit_with) ? Action::EDIT_WITH_ELEMEBTOR : $edit_with;

		return $edit_with === self::EDIT_WITH_GUTENBERG;
	}
}
