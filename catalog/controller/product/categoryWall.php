<?php
class ControllerProductCategoryWall extends Controller {
	public function index() {
		$this->load->language('product/categoryWall');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');


		$meta_data = $this->config->get('module_CategoryWall_seo');
		$description = $this->config->get('module_CategoryWall_description');

		$currrentLang = $this->config->get('config_language_id');
		$this->document->setTitle($meta_data[$currrentLang]["meta_title"]);
		$this->document->setDescription($meta_data[$currrentLang]["meta_description"]);
		$this->document->setKeywords($meta_data[$currrentLang]["meta_keyword"]);
		
		$this->document->addLink($this->url->link('product/categoryWall'), 'canonical');
		

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$module_name = $description[$currrentLang]["title"] ? $description[$currrentLang]["title"] : $this->language->get('heading_title');
		$data['breadcrumbs'][] = array(
			'text' => $module_name,
			'href' => $this->url->link('product/categoryWall')
		);

		$data['title'] = $description[$currrentLang]["title"];
		$data['description'] = html_entity_decode($description[$currrentLang]["description"], ENT_QUOTES, 'UTF-8');
		$data['categories'] = array();

		$categories = $this->config->get('module_CategoryWall_category');

		if ($categories) {
			foreach ($categories as $cat_id => $item) {
				$category = $this->model_catalog_category->getCategory($item['id']);
				$filter_data = array(
					'filter_category_id'  => $category['category_id'],
					'filter_sub_category' => true
				);
	
				if ($category['image']) {
					$cat_image = $this->model_tool_image->resize($category['image'], $this->config->get('module_CategoryWall_width'), $this->config->get('module_CategoryWall_height'));
				} else {
					$cat_image = $this->model_tool_image->resize('placeholder.png', $this->config->get('module_CategoryWall_width'), $this->config->get('module_CategoryWall_height'));
				}
				
				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					'image' 	  => $cat_image,
					'name'        => $category['name'] . ($this->config->get('module_CategoryWall_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href'        => $this->url->link('product/category', 'path=' . $category['category_id'])
				);
			}
		}

		$data['qtyInRow'] = $this->config->get('module_CategoryWall_qtyInRow');
		$data['min_width'] = $this->config->get('module_CategoryWall_minWidth');
		
		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('product/categoryWall', $data));
		
	}
}