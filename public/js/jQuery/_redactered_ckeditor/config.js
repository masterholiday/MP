CKEDITOR.editorConfig = function( config )
{
	config.fullPage = false;
	
	config.uiColor = '#c8c8c8';
	
	config.width = 800;						
	config.resize_maxWidth = 800;			
	config.resize_minWidth = 800;
//	
//	config.baseHref = 'http://aurora-investment.ru/';
//	
	config.height = 200;
	config.resize_maxHeight = 200;
	config.resize_minHeight = 200;
	
	config.language = 'ru';

	config.toolbar = 'Full';
	
	//Basic toolbar
	config.toolbar_Basic =
	[
		[ 'Source', '-', 'Bold', 'Italic' ]
	];
	
	config.toolbar_Full =
	[
		{ name: 'clipboard',   items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
		{ name: 'editing',     items : [ 'Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
		{ name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
		{ name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
		{ name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
		'/',
		{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
		{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];

	config.bodyId = 'main_textarea_data';		//Sets the id attribute to be used on the body element of the editing area. This can be useful when you intend to reuse the original CSS file you are using on your live website and want to assign the editor the same ID as the section that will include the contents. In this way ID-specific CSS rules will be enabled.

	config.filebrowserWindowFeatures = 'resizable=no,scrollbars=no';

	config.font_names =
		'Arial/Arial, Helvetica, sans-serif;' +
		'Times New Roman/Times New Roman, Times, serif;' +
		'Verdana';

	config.font_names = 'Arial;Times New Roman;Verdana';

	config.fontSize_defaultLabel = '12px';

	config.fontSize_sizes = '8 Pixels/8px;9 Pixels/9px;10 Pixels/10px;11 Pixels/11px;12 Pixels/12px;13 Pixels/13px;14 Pixels/14px;15 Pixels/15px;16 Pixels/16px;17 Pixels/17px;18Pixels/18px;19 Pixels/19px;20 Pixels/20px;21 Pixels/21px;22 Pixels/22px;23 Pixels/23px;24 Pixels/24px;25 Pixels/25px;';

	config.indentUnit = 'px';

	config.menu_groups = 'clipboard,table,anchor,link,image';
	//Default Value: 'clipboard,form,tablecell,tablecellproperties,tablerow,tablecolumn,table,anchor,link,image,flash,checkbox,radio,textfield,hiddenfield,imagebutton,button,select,textarea' 

	config.newpage_html = '<p>Введите сюда текст...</p>';
	//Default Value: '' 

	config.protectedSource.push( /<\?[\s\S]*?\?>/g );   // PHP code

	// Default settings.
	/*config.smiley_descriptions =
		[
			'smiley', 'sad', 'wink', 'laugh', 'frown', 'cheeky', 'blush', 'surprise',
			'indecision', 'angry', 'angel', 'cool', 'devil', 'crying', 'enlightened', 'no',
			'yes', 'heart', 'broken heart', 'kiss', 'mail'
		];

	// Use textual emoticons as description.
	config.smiley_descriptions =
		[
			':)', ':(', ';)', ':D', ':/', ':P', ':*)', ':-o',
			':|', '>:(', 'o:)', '8-)', '>:-)', ';(', '', '', '',
			'', '', ':-*', ''
		];*/

	//Defined in: plugins/smiley/plugin.js.

	// This is actually the default value.
	/*config.smiley_images = [
		'regular_smile.gif','sad_smile.gif','wink_smile.gif','teeth_smile.gif','confused_smile.gif','tounge_smile.gif',
		'embaressed_smile.gif','omg_smile.gif','whatchutalkingabout_smile.gif','angry_smile.gif','angel_smile.gif','shades_smile.gif',
		'devil_smile.gif','cry_smile.gif','lightbulb.gif','thumbs_down.gif','thumbs_up.gif','heart.gif',
		'broken_heart.gif','kiss.gif','envelope.gif'];
*/
	//config.smiley_path = 'http://www.example.com/images/smileys/';

	//config.smiley_path = '/images/smileys/';

	//config.specialChars = [ '"', '’', [ '&custom;', 'Custom label' ] ];
	//config.specialChars = config.specialChars.concat( [ '"', [ '’', 'Custom label' ] ] );

	//config.startupFocus = true;
	//Default Value: false 

	//config.startupMode = 'source';
	//Default Value: 'wysiwyg' 

	//config.tabIndex = 1;

	//config.tabSpaces = 4;
	//Default Value: 0 

	//config.templates_replaceContent = false;
	//Default Value:  true 

	//config.theme = 'default';
	//Default Value: 'default' 

	// Defines a toolbar with only one strip containing the "Source" button, a
	// separator and the "Bold" and "Italic" buttons.
	/*config.toolbar =
	[
		[ 'Source', '-', 'Bold', 'Italic' ]
	];*/
};