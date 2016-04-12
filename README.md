
# Dynacase FreeEvent

## Description

Process temporal events for dynacase families.

## Documentation

### 1 -Introduction 

Le module FREEEVENT a pour objectif d'apporter un ensemble de
fonctionnalités à Dynacase pour représenter les documents en fonction de
critères de temps.

FREEEVENT introduit trois nouvelles familles :

*    événement
*    recherche d'événements
*    dossier d'événements

La famille **événement** contient les informations temporelles et descriptives
permettant la sélection et l'affichage de ces événements sous forme de planning
ou d'agenda.

Les documents événements ne sont pas produits directement par l'utilisateur mais
par des familles de document dites « productrices d'événements ».



### 2 -Événements

#### 2.1.définition

Un événement est un document issu de la famille événement ou de ses héritiers.
Il sert à réserver des ressources (humaines ou matérielles) dans un laps de
temps défini.

L'événement contient obligatoirement une date de début et une date de fin. Ces
dates ont une précision à la minute.

|            attribut           |                                                  description                                                  |
| ----------------------------- | ------------------------------------------------------------------------------------------------------------- |
| **titre**                     | descriptif court de l'événement                                                                               |
| description                   | descriptif long de l'événement                                                                                |
| type                          | catégorie de l'événement. Ces catégories sont fonctions de la famille référent                                |
| **date début**                | date du début de l'événement à la minute : exemple : 10/12/2004 12:54                                         |
| **date de fin**               | date du fin de l'événement à la minute : exemple : 11/12/2004 23:54                                           |
| créateur/propriétaire         | par défaut c'est l'utilisateur qui a créé l'événement                                                         |
| ressources                    | ce sont la liste des documents ressources impactés par l'événement                                            |
| famille référent              | la famille qui a servi à produire l'événement                                                                 |
| document référent             | le document qui a produit l'événement                                                                         |
| fonction de transfert         | la méthode qui a produit l'événement (pEventDefault par défaut)                                               |
| fonction de transfert inverse | la méthode qui sert pour modifier le document référent à partir de l'événement. (Non utilisée pour l'instant) |

Il ne peut exister qu'un seul événement par document producteur et par méthode
de transfert. Si l'événement associé à un producteur existe déjà avec la méthode
de transfert utilisé alors il est modifié.

#### 2.2.ressource 

Les ressources peuvent être n'importe quel document marqué comme tel. Pour
marquer un document comme ressource il suffit de lui assigner le marquage
applicatif 'R' (propriétés `atags`). Ce marquage n'est pas modifiable par
l'interface actuellement. Si une famille est marquée 'R', tous les documents
produits auront le même marquage.

Pour marquer une famille il est nécessaire de passer par le fichier
d'importation avec la définition suivante. L'exemple suivant indique que les
utilisateurs peuvent être des ressources.

| BEGIN |     |     |     |     | IUSER |
| ----- | --- | --- | --- | --- | ----- |
| TAG   | R   |     |     |     |       |
| END   |     |     |     |     |       |



Ce marquage ne modifie pas les utilisateurs existants. Pour indiquer que les
utilisateurs existants sont aussi des ressources, il est nécessaire de modifier
directement la base de données.

<code>
[root@host root]# PGSERVICE=test psql
Bienvenue dans psql 7.4.2, l'interface interactive de PostgreSQL.

test=# UPDATE doc128 set atags = 'R';
UPDATE 2233

</code>

### 3 -Producteurs 

Les familles productrices sont celles qui sont capable d'engendrer des
événements. La déclaration d'une famille productrice doit utiliser le trait
 **`\Dcp\Freeevent\EventProduct`** dans sa classe associée.

| BEGIN  |                     | Demande de congés |     |     | ABSENCE |
| ------ | ------------------- | ----------------- | --- | --- | ------- |
| TAG    | P                   |                   |     |     |         |
| CLASS  | My\Absence          |                   |     |     |         |
| END    |                     |                   |     |     |         |

Ces familles doivent être marquées **`P`** (comme productrice), ceci dans le but
de pouvoir les rechercher suivant ce critère. Ces familles doivent hériter des
méthodes de production d'événements. Par défaut, une seule fonction de
production est disponible (pEventDefault). Cette fonction de production est
appelée par la méthode ::setEvent(). Le fait d'utiliser le trait
`\Dcp\Freeevent\EventProduct` dans la classe de production ne permet pas la
production directement.

La famille productrice doit surcharger les méthodes pour remplir les attributs
de l'événement et elle doit indiquer quand elle doit produire, modifier et
supprimer cet événement (appel à ::setEvent(), ::deleteEvent() ).

#### 3.1.Événement atomique 

Si la famille productrice a des attributs qui correspondent directement à des
attributs de l'événement, elle peut utiliser les attributs suivants pour
indiquer la relation :

  * $eventAttBeginDate : date de début
  * $eventAttEndDate : date de fin
  * $eventAttDesc : description longue
  * $eventAttCode : catégorie
  * $eventRessources : tableau de ressource;

Si certains attributs ne correspondent pas, la famille productrice doit alors redéfinir les méthodes suivantes :

  * ::getEventBeginDate() : retourne la date de début, si pas de minutes c'est la date à 00:00 heures.
  * ::getEventEndDate() : retourne la date de fin
  * ::getEventOwner() : retourne l'identificateur FREEDOM du propriétaire (par défaut basé sur propriété owner du producteur
  * ::getEventTitle() : retourne la titre (par défaut titre du producteur)
  * ::getEventDesc() : retourne la description (vide par défaut basé sur $eventAttDesc)
  * ::getEventCode() : retourne la catégorie (vide par défaut basé sur $eventAttCode)
  * ::getEventRessources() : retourne un tableau des identifiants des documents ressources.

  * ::getOverDescription() : retourne une portion de code HTML qui décrit l'événement lorsque que le curseur de la souris passe sur la représentation de l'événement. Par défaut la description est basée sur Event::getEventDesc().
  * ::getContextMenu() : retourne la description du menu contextuel lié à l'événement
  * ::getSelectAction() : retourne l'action qui va être réalisée lors d'un clic de l'événement 
      **                               syntaxe : soit ''url:<url composé>'' exemple 
        * ''url:%S%app=TEST&action=MONACTION&id=%I%''
        * ''url:%S%app=TEST&action=MONACTION_DU_PRODUCTEUR&id=%EVT_IDINITIATOR%''
        * ''javascript:execution(event,'%I%')''
        * **''url:%S%app=FDL&action=FDL_CARD&id=%EVT_IDINITIATOR%''** (par défaut)
       






Exemple Class-Absence.php :

``` php
namespace My;

class Absence extends \Dcp\Family\Document
{
    use \Dcp\Freeevent\EventProduct;
    
    public function __construct($dbaccess = '', $id = '', $res = '', $dbid = 0)
    {
        parent::__construct($dbaccess, $id, $res, $dbid);
        
        $this->eventAttBeginDate = "ABS_BEGDATE";
        $this->eventAttEndDate = "ABS_ENDDATE";
        $this->eventAttDesc = "ABS_DESC";
        $this->eventAttCode = "ABS_DESC";
        $this->eventRessources = array(
            "ABS_IDDEMAND","ABS_IDVAL","ABS_IDDIR","ABS_IDRH"
        );
    }
    
    public function getEventEndDate()
    {
        return substr($this->getRawValue($this->eventAttEndDate) , 0, 10) . " 23:59:59";
    }
  
    public function postStore()
    {
        $this->setEvent();
    }
}
```


#### 3.2.Événement répétable 

Si une famille productrice doit générer des événements répétables elle doit donner tous les éléments de répétabilité à l'événement pour qu'il puisse se découper. Il faut au préalable créer une famille héritée d'événement qui doit contenir les attributs contenant les informations de répétabilité.

Soit la famille événement journalier qui permet de répéter un événement suivant le jour de la semaine.

| BEGIN |   "EVENT"   | Évenement journalier |              |     | DAYEVENT |       |     |     |      |      |         |                                                                   |
| ----- | ----------- | -------------------- | ------------ | --- | -------- | ----- | --- | --- | ---- | ---- | ------- | ----------------------------------------------------------------- |
| CLASS | My\DayEvent |                      |              |     |          |       |     |     |      |      |         |                                                                   |
|       | *idattr*    | idframe              | label        | T   | A        | type  | ord | vis | need | link | phpfile | phpfunc                                                           |
| ATTR  |             |                      | Répétabilité | N   | N        | Frame | 200 | W   | N    |      |         |                                                                   |
| ATTR  | DEVT_DAY    | DEVT_FR_REPEAT       | Jour         | N   | N        | enum  |     |     |      |      |         | 1¦Lundi,2¦Mardi,3¦Mercredi,4¦Jeudi,5¦Vendredi,6¦Samedi,7¦Dimanche |
| END   |             |                      |              |     |          |       |     |     |      |      |         |                                                                   |



Cette famille d'événement doit redéfinir la méthode explodeEvt() pour produire
plusieurs événements atomiques à partir d'un événement répétable.

``` php
namespace My;

class DayEvent extends \Dcp\Family\Event
{
    public function explodeEvt($d1, $d2)
    {
        include_once ("FDL/Lib.Util.php");
        
        $jdi1 = ($d1 == "") ? 0 : Iso8601ToJD($d1);
        $jdi2 = StringDateToJD($this->getRawValue("evt_begdate"));
        $jd1 = max($jdi1, $jdi2); // search begin date
        $jdi1 = ($d2 == "") ? 5000000 : Iso8601ToJD($d2);
        $jdi2 = StringDateToJD($this->getRawValue("evt_enddate"));
        $jd2 = min($jdi1, $jdi2); // search end date
        $day = intval($this->getRawValue("DEVT_DAY")); // the day to repeat
        if (($day < 1) || ($day > 7)) {
            throw new \Exception("error day $day");
        }
        $djd1 = jdWeekDay($jd1);
        $jd1+= ($day - $djd1 + 7) % 7; // search the first day
        $te = array();
        $te1 = parent::explodeEvt($d1, $d2);
        
        for ($i = $jd1; $i < $jd2; $i+= 7) {
            $te[$i] = $te1[0];
            $te[$i]["evt_begdate"] = jd2cal($i); // change date period
            $te[$i]["evt_enddate"] = jd2cal($i + 1); // one day later
            $te[$i]["evt_desc"] = $i;
        }
        
        return $te;
    }
}
```

Avec cette famille, l'événement "manger des patates tous les mardis" pourra être
créé à partir de la famille productrice "menu journalier" par exemple.



Soit la définition suivante de la famille "menu journalier" :



| BEGIN |               | Menu journalier |                |     | DAYMENU |       |     |     |      |      |         |                                                                   |
| ----- | ------------- | --------------- | -------------- | --- | ------- | ----- | --- | --- | ---- | ---- | ------- | ----------------------------------------------------------------- |
| TAG   | P             |                 |                |     |         |       |     |     |      |      |         |                                                                   |
| CLASS | My\DayMenu    |                 |                |     |         |       |     |     |      |      |         |                                                                   |
|       | idattr        | idframe         | label          | T   | A       | type  | ord | vis | need | link | phpfile | phpfunc                                                           |
| ATTR  | ABS_FR_IDENT  |                 | Identification | N   | N       | frame | 200 | W   |      |      |         |                                                                   |
| ATTR  | ABS_DESC      | ABS_FR_IDENT    | description    | Y   | N       | text  | 210 | W   |      |      |         |                                                                   |
| ATTR  | ABS_IDUSER    | ABS_FR_IDENT    | id utilisateur | N   | N       | docid | 220 | H   |      |      |         |                                                                   |
| ATTR  | ABS_USER      | ABS_FR_IDENT    | utilisateur    | N   | N       | text  | 230 | W   |      |      | fdl.php | lfamily(D,USER,ABS_USER):ABS_IDUSER,ABS_USER                      |
| ATTR  | ABS_BEGDATE   | ABS_FR_IDENT    | date début     | N   | N       | date  | 240 | W   |      |      |         |                                                                   |
| ATTR  | ABS_ENDDATE   | ABS_FR_IDENT    | date fin       | N   | N       | date  | 250 | W   |      |      |         |                                                                   |
| ATTR  | ABS_FR_REPEAT |                 | Répétabilité   | N   | N       | frame | 260 | W   |      |      |         |                                                                   |
| ATTR  | ABS_DAY       | ABS_FR_REPEAT   | jour           | N   | N       | enum  | 270 | W   |      |      |         | 1¦Lundi,2¦Mardi,3¦Mercredi,4¦Jeudi,5¦Vendredi,6¦Samedi,7¦Dimanche |
| END   |               |                 |                |     |         |       |     |     |      |      |         |                                                                   |

La famille productrice en plus de mettre à jour les attributs de l'événement «
brut » doit aussi mettre à jour les informations nécessaire pour la
répétabilité. Ceci peut être fait en surchargeant la méthode «::setEventSpec().

Voici un exemple de production de répétable avec la famille « //menu journalier// ».

``` php
namespace My;

class DayMenu extends \Dcp\Family\Document
{
    use \Dcp\Freeevent\EventProduct;
    
    public function __construct($dbaccess = '', $id = '', $res = '', $dbid = 0)
    {
        parent::__construct($dbaccess, $id, $res, $dbid);
        
        $this->eventAttBeginDate = "DAYM_BEGDATE";
        $this->eventAttEndDate = "DAYM_ENDDATE";
        $this->eventAttDesc = "DAYM_DESC";
        $this->eventFamily = "DAYEVENT";
        $this->eventRessources = array(
            "DAYM_USER"
        );
    }
    
    public function getEventEndDate()
    {
        return substr($this->getRawValue($this->eventAttEndDate) , 0, 10) . " 23:59:59";
    }
    /**
     * Use for derived event by the producer to set added attributes
     * @param \Dcp\Freeevent\Event $e event object
     */
    public function setEventSpec(&$e)
    { //mise à jour attribut de répétabilité
        $e->setValue("DEVT_DAY", $this->getRawValue("DAYM_DAY"));
    }
    
    publicfunction postStore()
    {
        $this->setEvent();
    }
}

```


### 4. Affichages temporels des événements 

#### 4.1. Recherche d'événements 
 
La recherche d'événements est basée sur la recherche détaillée. Cette recherche
construit sa requête sur les critères tel que cela est fait avec la recherche
détaillé . De plus, la requête est aussi construite en fonction des critères
issus de l'édition par défaut tels que des contraintes de ressources ou de
famille productrices. Deux critères obligatoires sont aussi ajoutés sur la
recherche : date de début et date de fin non nulle pour les événements. Tous les
événements construits sans ces deux attributs obligatoires seront ignorés.
Puisque recherche d'événements hérite de recherche détaillé, il est possible
d'indiquer des paramètres ou des fonctions dans la requête. Pour utiliser ces
fonctionnalités, il est néanmoins nécessaire d'utiliser la vue d'édition
spéciale : détaillée. Dans cette vue, l'interface d'édition est la même que
celle de la recherche détaillée. Si vous utilisez cette vue d'édition, il faut
bien se souvenir que la requête complète sera faite en fonction des critères de
cette vue et de la vue par défaut d'édition. Il est préférable de ne pas
mélanger les deux vues pour ne pas avoir des critères contradictoires.

Cette famille dispose d'une vue de consultation sous forme de *planning*.

#### 4.2. Dossier d'événements 

Le dossier événement permet de rassembler des événements quelconques. La seule
différence avec un dossier classique est qu'il possède une vue //planning//
comme la recherche d'événement. L'ajout ou la suppression d'événement dans ce
dossier ce fait comme pour un dossier classique. Le dossier événements ne peut
contenir que des événements.

### Migration depuis la version 2.8

Les fichiers *Méthode* , notamment le fichier `Method.PEvents.php` ne sont plus
livrés par le module. Les producteurs d'événement doivent dorénavent utiliser le
trait `\Dcp\Freeevent\EventProduct` au lieur de la méthode "*Method.PEvents.php".


La surcharge des attributs  :

*    $eventAttBeginDate : date de début
*    $eventAttEndDate : date de fin
*    $eventAttDesc : description longue
*    $eventAttCode : catégorie
*    $eventRessources : tableau de ressource;
  
doit être indiquée dans le contructeur de la classe de la famille.

L'ancienne documentation pour la version 2.8 est accessible dans le fichier
[README-2.8.md](README-2.8.md). 

## Licence

Merci de vous référer au fichier [LICENSE](LICENSE) pour connaitre les droits
de modification et de distribution du module et de son code source.

La licence s'applique à l'ensemble des codes source du module. 

Elle prévaut sur toutes licences qui pourraient être mentionnées dans certains
fichiers.
