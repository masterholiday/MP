<?php

class Zend_View_Helper_Catalog extends Zend_View_Helper_Abstract {

	public function catalog($list, $hideTitle = false)
	{
        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        ob_start();
        ?>
            <div class="catalog-short-list">
                <?php if(!$hideTitle) { ?><h2>Каталог</h2> <?php } ?>
                <div class="box-out">
                    <div class="box-in">
                        <div class="subcat">
                            <div class="light">
                                <div class="dark">
                                    <a class="back">&lt; назад</a>
                                    <span></span>
                                </div>
                            </div>
                            <?php
                            foreach ($list as $c) {
                                echo $view->shortSubcatalog($c);
                            }
                            ?>
                        </div>
                        <div class="main-list">
                            <?php echo $view->shortCatalog($list)?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        $s = ob_get_clean();
        return $s;
	}
}