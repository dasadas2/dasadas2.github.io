<?php
#####################################################################################
#  Module CSV IMPORT PRO for Opencart 1.5.0 From HostJars opencart.hostjars.com 	#
#####################################################################################

// Heading
$_['heading_title']    = 'Загрузка прайсов поставщиков CSV Import PRO+ ';
$_['introcsv']    = 'Прайс поставщика может быть с любым расположением столбцов, Вы должны добавить пустую строку в начало файла и обозначить столбцы соответственно их содержимому(подробнее см. раздел ниже "Настройка полей")<br />
<b>!!!для экономии процессорного времени хостинга НЕНУЖНЫЕ СТОЛБЦЫ - УДАЛИТЬ!!!</b>
<br />
Пример файла шаблона загрузки доступен по ссылке: <a href="/admin/controller/common/Шаблон загрузки.csv">Загрузить файл шаблон прайс-листа</a>';

// Tabs
$_['tab_config']    = 'Глобальные настройки';
$_['tab_map']       = 'Настройка полей';
$_['tab_adjust']    = 'Коррекция данных';
$_['tab_import']    = 'Импорт';
$_['tab_export']    = 'Экпорт';

// Text
$_['text_csv_import_menu']  = 'Импорт CSV';
$_['text_success']          = 'Операция выполнена успешно: добавлено <b>%s</b> | изменено <b>%s</b> | пропущено <b>%s</b> | потеряно <b>%s</b> товаров';
$_['text_add']              = 'Добавить товары';
$_['text_reset']            = 'Удалить товары';
$_['text_fullreset']            = 'Очистить БД';
$_['text_update']           = 'Добавить/Обновить';
$_['text_update2']          = 'Только обновить';
$_['text_notes']            = 'Файл экпорта имеет следующий формат: <br />
<br/ >Разделитель полей знак "<strong>точка с запятой</strong>", разделитель строк "<strong>перенос строки</strong>". <br />
<p>Экпортируются следующие поля поля:<br />
<b>category, sku, model, name, manufacturer, price, special, quantity, image, description, status, href (ссылка на товар)</b></p>';


// Entry

$_['entry_import_file']     = 'Путь к CSV файлу на компьютереe:';
$_['entry_import_url']      = 'Загрузить файл по ссылке URL:';
$_['entry_stock_status']    = 'Наличие по умолчанию для загружаемых товаров:';
$_['entry_weight_class']    = 'Вес (по умолчанию):';
$_['entry_length_class']    = 'Длина (по умолчанию):';
$_['entry_tax_class']       = 'Default Tax Class:';
$_['entry_subtract']        = 'Subtract Stock:';
$_['entry_product_status']  = 'Статус товара (по умолчанию):';
$_['entry_language']        = 'Язык:';
$_['entry_ignore_fields']   = 'Пропустить товары где:';
$_['entry_store']  	        = 'Магазины:';
$_['entry_remote_images']  	= 'Загружать картинки:';
$_['entry_remote_images_warning'] = 'Warning: Возможен значительный тайм-аут при обработке более 500 товаров';
$_['entry_delimiter']           = 'Разделитель рядов данныз в CSV:';
$_['entry_escape']              = 'CSV Escape Character:';
$_['entry_qualifier']           = 'CSV Text Qualifier:';
$_['entry_data_feed']           = 'CSV Data Feed:';
$_['entry_field_mapping']       = 'Field Mapping:';
$_['entry_import_type']         = 'Как загружать товары:';
$_['entry_price_multiplier']    = 'Price Multiplier:';
$_['entry_image_remove']        = 'Image Remove Text:';
$_['entry_image_prepend']       = 'Image Prepend Text:';
$_['entry_image_append']        = 'Image Append Text:';
$_['entry_split_category']      = 'Разделиитель категорий товаров:';
$_['entry_top_categories']      = 'Категории в главное меню:<br/><span class="help">Показывать в главном меню (только для главных родительских категорий)</span>';
$_['entry_convert_status']      = 'Конвертировать в UTF-8 перед обработкой:';

$_['entry_export']		        = 'Экспорт:';
$_['entry_category']	        = 'Экспорт из категорий:';
$_['entry_category_help']       = 'Если категории не выбраны - экспортирует все товары, <br>файл выгружается в кодировке Windows-1251';
$_['entry_format']		        = 'Формат файла:';

// Field Names
$_['text_field_oc_title']	    = 'Значения из OpenCart';
$_['text_field_csv_title']	    = 'Идентификаторы столбцов в файле CSV';
$_['text_field_name']           = '<b>Название</b> <br>(например name)';
$_['text_field_price']          = '<b>Цена</b> <br>(например price)';
$_['text_field_model']          = '<b>Оригинальный номер</b> <br>(например number)';
$_['text_field_manufacturer']   = '<b>Производитель</b> <br>(например proizvod)';
$_['text_field_category']       = '<b>Категория</b>';
$_['text_field_quantity']       = '<b>Количество</b> <br>(например kolvo)';
$_['text_field_quantity_class_id'] = '<b>Единица измерения кол-ва</b> <br>(например edca)';
$_['text_field_minimum']        = '<b>Минимальное количество:</b>';
$_['text_field_image']          = '<b>Фото товара</b> <br>(например фото)';
$_['text_field_description']    = '<b>Описание</b> <br>(например opisanie)';
$_['text_field_meta_desc']      = '<b>Meta Description</b> <br>(например deskr)';
$_['text_field_meta_keyw']      = '<b>Meta Keywords</b> <br>(например kw)';
$_['text_field_weight']         = '<b>Вес</b> <br>(например ves)';
$_['text_field_tags']           = '<b>Теги</b> <br>(например tegi)';
$_['text_yes']                  = 'Да';
$_['text_no']                   = 'Нет';


// Import
$_['button_import']	   = 'Импорт';
$_['button_export']	   = 'Экпорт';
$_['button_save'] 	   = 'Сохранить';
$_['button_cancel']	   = 'Отмена';


// Error
$_['error_permission'] = 'У Вас нет прав для управления этим модулем!';
$_['error_empty']      = 'Загруженный файл пуст!';
?>