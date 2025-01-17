<div class="cnc_admin">
<img src="<?=$this->logo()?>" class="cnc_logo" alt="<?=$this->text('alt_logo')?>">
<h1>CnC_XH</h1>
<p class="cnc_version">
    Version: <?=$this->version()?><br>&copy; 2017 Holger Irmler<br>&copy; 2025 CMSimple_XH developers
</p>
<p class="cnc_license">
    <strong>Cache & Compress for CMSimple_XH</strong><br>
    uses <a href="https://github.com/mrclay/minify">Minify</a>, an HTTP server for 
    JS and CSS assets. It compresses and combines files and serves it with 
    appropriate headers, allowing conditional GET or long-Expires. 
    Minify is written by <a href="http://www.mrclay.org/">Steve Clay</a> and
    distributed under the BSD 3-clause "New" or "Revised" License.
</p>
<hr>
<p class="cnc_license">
    CnC_XH is free software: you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published by the Free
    Software Foundation, either version 3 of the License, or (at your option)
    any later version.
</p>
<p class="cnc_license">
    CnC_XH is distributed in the hope that it will be useful, but
    <em>without any warranty</em>; without even the implied warranty of
    <em>merchantability</em> or <em>fitness for a particular purpose</em>. See
    the GNU General Public License for more details.
</p>
<p class="cnc_license">
    You should have received a copy of the GNU General Public License along with
    CnC_XH. If not, see <a
    href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
</p>
</div>
<div class="cnc_syscheck">
    <h2><?=$this->text('syscheck_title')?></h2>
<?php foreach ($this->checks as $check):?>
    <p class="xh_<?=$this->escape($check->state)?>"><?=$this->text('syscheck_message', $check->label, $check->stateLabel)?></p>
<?php endforeach?>
</div>
