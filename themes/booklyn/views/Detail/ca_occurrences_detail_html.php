<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/ca_occurrences_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	$t_occurrence 			= $this->getVar('t_item');
	$vn_occurrence_id 	= $t_occurrence->getPrimaryKey();
	
	$vs_title 					= $this->getVar('label');
	
	$va_access_values	= $this->getVar('access_values');

if (!$this->request->isAjax()) {
?>
	<div id="detailBody">
		<div id="pageNav">
<?php
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_occurrences', _t("Back"), ''))) {
				if ($this->getVar('previous_id')) {
					print caNavLink($this->request, "&lsaquo; "._t("Previous"), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('previous_id')), array('id' => 'previous'));
				}else{
					print "&lsaquo; "._t("Previous");
				}
				print "&nbsp;&nbsp;&nbsp;{$vs_back_link}&nbsp;&nbsp;&nbsp;";
				
				if ($this->getVar('next_id') > 0) {
					print caNavLink($this->request, _t("Next")." &rsaquo;", '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $this->getVar('next_id')), array('id' => 'next'));
				}else{
					print _t("Next")." &rsaquo;";
				}
			}
?>
		</div><!-- end nav -->
		<h1><?php print unicode_ucfirst($t_occurrence->getTypeName()).': '.$vs_title; ?></h1>
		<div id="leftCol">	
<?php
			if($this->request->config->get('enable_bookmarks')){
?>
				<!-- bookmark link BEGIN -->
				<div class="unit">
<?php
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Bookmark +"), 'button', '', 'Bookmarks', 'addBookmark', array('row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}else{
					print caNavLink($this->request, _t("Bookmark +"), 'button', '', 'LoginReg', 'form', array('site_last_page' => 'Bookmarks', 'row_id' => $vn_occurrence_id, 'tablename' => 'ca_occurrences'));
				}
?>
				</div><!-- end unit -->
				<!-- bookmark link END -->
<?php
			}
			# --- identifier
#			if($t_occurrence->get('idno')){
#				print "<div class='unit'><span class='metatitle'>"._t("Identifier")."</span><br/> ".$t_occurrence->get('idno')."</div><!-- end unit -->";
#			}
			if ($va_venue = $t_occurrence->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('venue'), 'delimiter' => '<br/>', 'checkAccess' => $va_access_values, 'sort' => 'surname'))) {
				print "<div class='unit'><span class='metatitle'>Venue</span><br/>".$va_venue."</div>";
			}
			if ($va_dates = $t_occurrence->get('ca_occurrences.exhibition_date.exhDatesValue', array('delimiter' => '<br/>'))) {
				print "<div class='unit'><span class='metatitle'>Exhibition Dates</span><br/>".$va_dates."</div>";
			}			
			if ($va_curator = $t_occurrence->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('curator'), 'delimiter' => '<br/>', 'checkAccess' => $va_access_values, 'sort' => 'surname'))) {
				print "<div class='unit'><span class='metatitle'>Curator</span><br/>".$va_curator."</div>";
			}
			# --- description
			if($this->request->config->get('ca_occurrences_description_attribute')){
				if($vs_description_text = $t_occurrence->get("ca_occurrences.".$this->request->config->get('ca_occurrences_description_attribute'))){
					if (strlen($vs_description_text) <= 300) {
						print "<div class='unit'><span class='metatitle'>Description</span><br/> {$vs_description_text}</div><!-- end unit -->";				
					} else {
						$vs_short_description = substr($vs_description_text, 0, 400);
						print "<div class='unit' id='short'><span class='metatitle' >Description</span><br/> {$vs_short_description} ";?><a href='#' onclick='$("#long").show(); $("#short").hide()'> [more]</a></div><!-- end unit -->				
<?php						
						print "<div class='unit' style='display:none;' id='long'><span class='metatitle'>Description</span><br/> {$vs_description_text} ";?><a href='#' onclick='$("#short").show(); $("#long").hide()'> [less]</a></div><!-- end unit -->				
<?php
					}
				}
			}			
			if ($va_artists = $t_occurrence->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('artist', 'contributor'), 'delimiter' => '<br/>', 'checkAccess' => $va_access_values, 'sort' => 'surname', 'returnAsLink' => true))) {
				print "<div class='unit'><span class='metatitle'>Artists + Contributors</span><br/>".$va_artists."</div>";
			}
			if ($va_collaborator = $t_occurrence->get('ca_entities.preferred_labels', array('restrictToRelationshipTypes' => array('collaborator'), 'delimiter' => '<br/>', 'checkAccess' => $va_access_values, 'sort' => 'surname', 'returnAsLink' => true))) {
				print "<div class='unit'><span class='metatitle'>Collaborators</span><br/>".$va_collaborator."</div>";
			}	
	
							
			# --- attributes
			$va_attributes = $this->request->config->get('ca_occurrences_detail_display_attributes');
			if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
				foreach($va_attributes as $vs_attribute_code){
					if($vs_value = $t_occurrence->get("ca_occurrences.{$vs_attribute_code}")){
						print "<div class='unit'><span class='metatitle'>".$t_occurrence->getDisplayLabel("ca_occurrences.{$vs_attribute_code}")."</span><br/> {$vs_value}</div><!-- end unit -->";
					}
				}
			}


#			# --- entities
#			$va_entities = $t_occurrence->get("ca_entities", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
#			if(sizeof($va_entities) > 0){	
?>
<!--				<div class="unit"><span class='metatitle'><?php print _t("Related")." ".((sizeof($va_entities) > 1) ? _t("Entities") : _t("Entity")); ?></span><br/> -->
<?php
#				foreach($va_entities as $va_entity) {
#					print "<p>".(($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_entity["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_entity["entity_id"])) : $va_entity["label"])." ".$va_entity["relationship_typename"]."</p>";
#				}
?>
<!--				</div> end unit -->
<?php
#			}
			
			# --- occurrences
			$va_occurrences = $t_occurrence->get("ca_occurrences", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			$va_sorted_occurrences = array();
			if(sizeof($va_occurrences) > 0){
				$t_occ = new ca_occurrences();
				$va_item_types = $t_occ->getTypeList();
				foreach($va_occurrences as $va_occurrence) {
					$t_occ->load($va_occurrence['occurrence_id']);
					$va_sorted_occurrences[$va_occurrence['item_type_id']][$va_occurrence['occurrence_id']] = $va_occurrence;
				}
				
				foreach($va_sorted_occurrences as $vn_occurrence_type_id => $va_occurrence_list) {
?>
						<div class="unit"><span class='metatitle'><?php print _t("Related")." ".$va_item_types[$vn_occurrence_type_id]['name_singular'].((sizeof($va_occurrence_list) > 1) ? "s" : ""); ?></span><br/>
<?php
					foreach($va_occurrence_list as $vn_rel_occurrence_id => $va_info) {
						print "<p>".(($this->request->config->get('allow_detail_for_ca_occurrences')) ? caNavLink($this->request, $va_info["label"], '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_rel_occurrence_id)) : $va_info["label"])." (".$va_info['relationship_typename'].")</p>";
					}
					print "</div><!-- end unit -->";
				}
			}
			# --- places
			$va_places = $t_occurrence->get("ca_places", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_places) > 0){
				print "<div class='unit'><h2>"._t("Related Place").((sizeof($va_places) > 1) ? "s" : "")."</h2>";
				foreach($va_places as $va_place_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_places')) ? caNavLink($this->request, $va_place_info['label'], '', 'Detail', 'Place', 'Show', array('place_id' => $va_place_info['place_id'])) : $va_place_info['label'])." (".$va_place_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- collections
			$va_collections = $t_occurrence->get("ca_collections", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_collections) > 0){
				print "<div class='unit'><h2>"._t("Related Collection").((sizeof($va_collections) > 1) ? "s" : "")."</h2>";
				foreach($va_collections as $va_collection_info){
					print "<div>".(($this->request->config->get('allow_detail_for_ca_collections')) ? caNavLink($this->request, $va_collection_info['label'], '', 'Detail', 'Collection', 'Show', array('collection_id' => $va_collection_info['collection_id'])) : $va_collection_info['label'])." (".$va_collection_info['relationship_typename'].")</div>";
				}
				print "</div><!-- end unit -->";
			}
			# --- vocabulary terms
			$va_terms = $t_occurrence->get("ca_list_items", array("returnAsArray" => 1, 'checkAccess' => $va_access_values));
			if(sizeof($va_terms) > 0){
				print "<div class='unit'><span class='metatitle'>"._t("Subject").((sizeof($va_terms) > 1) ? "s" : "")."</span><br/>";
				foreach($va_terms as $va_term_info){
					print "<p>".caNavLink($this->request, $va_term_info['label'], '', '', 'Search', 'Index', array('search' => $va_term_info['label']))."</p>";
				}
				print "</div><!-- end unit -->";
			}
?>
	</div><!-- end leftCol -->
			
	<div id="rightCol">

		<div id="resultBox">
<?php
}
		// set parameters for paging controls view
		$this->setVar('other_paging_parameters', array(
			'occurrence_id' => $vn_occurrence_id
		));
		print $this->render('related_objects_grid.php');

if (!$this->request->isAjax()) {
?>
		</div><!-- end resultBox -->


	</div><!-- end rightCol -->
	<div class='seeMore' style='margin:10px 0px 10px 0px'><a href='#'>Back to Top</a></div>
</div><!-- end detailBody -->
<?php
}
?>