<?php
namespace Dcp\Family {
	/** Événement  */
	class Event extends \Dcp\Freeevent\Event { const familyName="EVENT";}
	/** recherche événement  */
	class Dcalendar extends \Dcp\Freeevent\Dcalendar { const familyName="DCALENDAR";}
	/** dossier événements  */
	class Scalendar extends \Dcp\Freeevent\Scalendar { const familyName="SCALENDAR";}
}
namespace Dcp\AttributeIdentifiers {
	/** Événement  */
	class Event {
		/** [frame] identification */
		const evt_fr_ident='evt_fr_ident';
		/** [text] titre */
		const evt_title='evt_title';
		/** [longtext] description */
		const evt_desc='evt_desc';
		/** [timestamp] date début */
		const evt_begdate='evt_begdate';
		/** [timestamp] date fin */
		const evt_enddate='evt_enddate';
		/** [docid] id créateur */
		const evt_idcreator='evt_idcreator';
		/** [text] créateur */
		const evt_creator='evt_creator';
		/** [array] ressources */
		const evt_t_res='evt_t_res';
		/** [docid] id ressource */
		const evt_idres='evt_idres';
		/** [text] ressource */
		const evt_res='evt_res';
		/** [text] type */
		const evt_code='evt_code';
		/** [docid] id famille référent */
		const evt_frominitiatorid='evt_frominitiatorid';
		/** [image] icone famille référent */
		const evt_frominitiatoricon='evt_frominitiatoricon';
		/** [text] famille référent */
		const evt_frominitiator='evt_frominitiator';
		/** [docid] id document référent */
		const evt_idinitiator='evt_idinitiator';
		/** [text] référent */
		const evt_initiator='evt_initiator';
		/** [text] fonction de transfert */
		const evt_transft='evt_transft';
		/** [text] fonction de transfert inverse */
		const evt_itransft='evt_itransft';
		/** [image] icone */
		const evt_icon='evt_icon';
	}
	/** recherche événement  */
	class Dcalendar extends Dsearch {
		/** [frame] paramètres de recherche */
		const dcal_fr_search='dcal_fr_search';
		/** [text] texte */
		const dcal_text='dcal_text';
		/** [enum] texte opérateur */
		const dcal_textop='dcal_textop';
		/** [docid] id producteur */
		const dcal_idproducer='dcal_idproducer';
		/** [text] producteur */
		const dcal_producer='dcal_producer';
		/** [text] famille d'événement */
		const se_fam='se_fam';
		/** [frame] ressources impactées */
		const dcal_fr_res='dcal_fr_res';
		/** [docid] id famille ressources */
		const dcal_idfres='dcal_idfres';
		/** [text] famille ressource */
		const dcal_fres='dcal_fres';
		/** [array] ressources */
		const dcal_t_res='dcal_t_res';
		/** [docid] id ressouce */
		const dcal_idres='dcal_idres';
		/** [text] ressource */
		const dcal_res='dcal_res';
		/** [enum] voir les ressources sélectionnées */
		const dcal_viewonlyres='dcal_viewonlyres';
		/** [frame] présentation */
		const dcal_fr_present='dcal_fr_present';
		/** [enum] couleurs */
		const dcal_coloridx='dcal_coloridx';
		/** [enum] luminance */
		const dcal_luminance='dcal_luminance';
		/** [enum] trié par */
		const dcal_orderidx1='dcal_orderidx1';
		/** [enum] sens du tri */
		const dcal_orderdesc1='dcal_orderdesc1';
		/** [enum] puis par */
		const dcal_orderidx2='dcal_orderidx2';
		/** [enum] sens du tri */
		const dcal_orderdesc2='dcal_orderdesc2';
		/** [enum] groupement */
		const dcal_groupby='dcal_groupby';
		/** [enum] voir le titre */
		const dcal_prestitle='dcal_prestitle';
		/** [enum] voir les icones */
		const dcal_presicon='dcal_presicon';
	}
	/** dossier événements  */
	class Scalendar extends Dir {
	}
}
