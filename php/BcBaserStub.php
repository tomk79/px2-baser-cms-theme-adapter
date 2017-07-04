<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class BcBaserStub{
	/** Pickles 2 Object */
	private $px = '';

	/**
	 * constructor
	 */
	public function __construct( $px ){
		$this->px = $px;
	} // __construct()

	public function docType(){}
	public function charset(){}
	public function title(){}
	public function css(){}
	public function js(){}
	public function scripts(){}
	public function googleAnalytics(){}
	public function contentsName(){}
	public function header(){}
	public function globalMenu(){}
	public function isHome(){}
	public function mainImage(){}
	public function crumbsList(){}
	public function flash(){}
	public function content(){}
	public function contentsNavi(){}
	public function widgetArea(){}
	public function footer(){}
	public function func(){}

}
