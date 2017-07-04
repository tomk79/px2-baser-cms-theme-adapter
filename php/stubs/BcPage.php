<?php
/**
 * px2-baser-cms-theme-adapter
 */
namespace tomk79\pickles2\baserCmsThemeAdapter;

/**
 * px2-baser-cms-theme-adapter
 */
class stubs_BcPage{
	/** Pickles 2 Object */
	private $px = '';

	/**
	 * constructor
	 *
	 * @param object $px Pickles 2 Object
	 */
	public function __construct( $px ){
		$this->px = $px;
	} // __construct()

	/**
	 * 前のページへのリンク
	 * @return void
	 */
	public function prevLink() {
		$goto = $this->px->site()->get_prev();
		if($goto === false){return;}
		$link = '<a href="'.htmlspecialchars($this->px->href($goto)).'" class="prev-link">';
		$link .= '≪ ';
		$link .= htmlspecialchars( $this->px->site()->get_page_info($goto, 'title_label') );
		$link .= '</a>';
		echo $link;
	}


	/**
	 * 次のページへのリンク
	 * @return void
	 */
	public function nextLink() {
		$goto = $this->px->site()->get_next();
		if($goto === false){return;}
		$link = '<a href="'.htmlspecialchars($this->px->href($goto)).'" class="next-link">';
		$link .= htmlspecialchars( $this->px->site()->get_page_info($goto, 'title_label') );
		$link .= ' ≫';
		$link .= '</a>';
		echo $link;
	}

}
