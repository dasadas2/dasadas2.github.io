<?php echo $header; ?>
<h1>Шаг 2 - Пред-установка</h1>
<div id="column-right">
  <ul>
    <li>Лицензионное соглашение</li>
    <li><b>Пред-установка</b></li>
    <li>Настройка</li>
    <li>Финиш</li>
  </ul>
</div>
<div id="content">
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data">
    <p>1. Настройки PHP.</p>
    <fieldset>
      <table>
        <tr>
          <th width="35%" align="left"><b>Опции</b></th>
          <th width="25%" align="left"><b>Ваша настройка</b></th>
          <th width="25%" align="left"><b>Рекомендованная</b></th>
          <th width="15%" align="center"><b>Статус</b></th>
        </tr>
        <tr>
          <td>PHP Version:</td>
          <td><?php echo phpversion(); ?></td>
          <td>5.0+</td>
          <td align="center"><?php echo (phpversion() >= '5.0') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>Register Globals:</td>
          <td><?php echo (ini_get('register_globals')) ? 'On' : 'Off'; ?></td>
          <td>Off</td>
          <td align="center"><?php echo (!ini_get('register_globals')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>Magic Quotes GPC:</td>
          <td><?php echo (ini_get('magic_quotes_gpc')) ? 'On' : 'Off'; ?></td>
          <td>Off</td>
          <td align="center"><?php echo (!ini_get('magic_quotes_gpc')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>File Uploads:</td>
          <td><?php echo (ini_get('file_uploads')) ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo (ini_get('file_uploads')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>Session Auto Start:</td>
          <td><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
          <td>Off</td>
          <td align="center"><?php echo (!ini_get('session_auto_start')) ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
      </table>
    </fieldset>
    <p>2. Дополнительные модули.</p>
    <fieldset>
      <table>
        <tr>
          <th width="35%" align="left"><b>Модуль</b></th>
          <th width="25%" align="left"><b>Ваша настройка</b></th>
          <th width="25%" align="left"><b>Рекомендованная</b></th>
          <th width="15%" align="center"><b>Статус</b></th>
        </tr>
        <tr>
          <td>MySQL:</td>
          <td><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo extension_loaded('mysql') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>GD:</td>
          <td><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo extension_loaded('gd') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>cURL:</td>
          <td><?php echo extension_loaded('curl') ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo extension_loaded('curl') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>mCrypt:</td>
          <td><?php echo function_exists('mcrypt_encrypt') ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo function_exists('mcrypt_encrypt') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
        <tr>
          <td>ZIP:</td>
          <td><?php echo extension_loaded('zlib') ? 'On' : 'Off'; ?></td>
          <td>On</td>
          <td align="center"><?php echo extension_loaded('zlib') ? '<img src="view/image/good.png" alt="Good" />' : '<img src="view/image/bad.png" alt="Bad" />'; ?></td>
        </tr>
      </table>
    </fieldset>
    <p>3. Настройка прав для файлов.</p>
    <fieldset>
      <table>
        <tr>
          <th align="left"><b>Файлы</b></th>
          <th align="left"><b>Статус</b></th>
        </tr>
        <tr>
          <td><?php echo $config_catalog; ?></td>
          <td><?php if (!file_exists($config_catalog)) { ?>
            <span class="bad">Missing</span>
            <?php } elseif (!is_writable($config_catalog)) { ?>
            <span class="bad">Unwritable</span>
          <?php } else { ?>
          <span class="good">Writable</span>
          <?php } ?>
            </td>
        </tr>
        <tr>
          <td><?php echo $config_admin; ?></td>
          <td><?php if (!file_exists($config_admin)) { ?>
            <span class="bad">Missing</span>
            <?php } elseif (!is_writable($config_admin)) { ?>
            <span class="bad">Unwritable</span>
          <?php } else { ?>
          <span class="good">Writable</span>
          <?php } ?>
             </td>
        </tr>
      </table>
    </fieldset>
    <p>4. Настройка прав для директорий.</p>
    <fieldset>
      <table>
        <tr>
          <th align="left"><b>Директория</b></th>
          <th align="left"><b>Статус</b></th>
        </tr>
        <tr>
          <td><?php echo $cache . '/'; ?></td>
          <td><?php echo is_writable($cache) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
        <tr>
          <td><?php echo $logs . '/'; ?></td>
          <td><?php echo is_writable($logs) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
        <tr>
          <td><?php echo $image . '/'; ?></td>
          <td><?php echo is_writable($image) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
        <tr>
          <td><?php echo $image_cache . '/'; ?></td>
          <td><?php echo is_writable($image_cache) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
        <tr>
          <td><?php echo $image_data . '/'; ?></td>
          <td><?php echo is_writable($image_data) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
        <tr>
          <td><?php echo $download . '/'; ?></td>
          <td><?php echo is_writable($download) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
        </tr>
          <tr>
            <td><?php echo $autosoft . '/'; ?></td>
            <td><?php echo is_writable($autosoft) ? '<span class="good">Writable</span>' : '<span class="bad">Unwritable</span>'; ?></td>
          </tr>          
      </table>
    </fieldset>
    <div class="buttons">
      <div class="left"><a href="<?php echo $back; ?>" class="button">Назад</a></div>
      <div class="right">
        <input type="submit" value="Continue" class="button" />
      </div>
    </div>
  </form>
</div>
<?php echo $footer; ?>
