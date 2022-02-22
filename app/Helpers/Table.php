<?php
namespace App\Helpers;

class Table
{
    /**
     * action responsible by generating Table in HTML from sent elements as indexed array
     * @author  NAB - 20200904  
     * @version 1.0 - 20200904 - initial release
     * @version 2.0 - 20200909 - html 'data-id' element and action buttons added
     * @params  $elements        <indexed array>  of elements - mandatory
     * @params  $columnsToHide   <array>  'index name' to hide by adding 'disabled' class. 
     *                                      'action'     to hide EDIT & DELETE buttons
        *                                   'filter'     to hide Filter
     *                                      'inUse'      to add "disabled" class to Delete button when element can't be deleted
     * 
     * @params  $parameters      <indexed array>  keys: 'sersd' To add a button in empty value with '-MISSING-' text and a 'empty' class to TD. Default is ' - '
     *                                                     'cssClass'        Name for the class of table. Default is 'pnhTable'
     * 
     * @returns $html            <string>         generated table in HTML
     */
    public function generateHTMLTable($elements, $columnsToHide = 'none', $parameters = null)
    {   
        // Verify if it's an Array
        if(!is_array($elements)){
            return 'indexed array is invalid';
        }
        // Verify if $columns to hide is valid
        if(empty($columnsToHide) || !is_array($columnsToHide))
            $columnsToHide = 'none';
        
        // Verify $parameters
        
        $addInsertButton = false;
        if(!empty($parameters['addInsertButton']))
            $addInsertButton = true;
        $cssClass = 'list-table';
        if(!empty($parameters['cssClass']))
            $cssClass = $parameters['cssClass'];
        $noDataElement = false;
        if(!empty($parameters['noDataElement']))
            $noDataElement = true;
        
        // instantiating $html return variable
        $html = '';
        
        // Returns default table if empty
        if(count($elements) < 1){
            $css = 'style="text-align:center;"';
            $html .= '<h6 class=" '.$cssClass.'" '.$css.'>no result found</h6>'
                  .  '<table class=" '.$cssClass.'"><th></th><th></th><th></th><th></th>'
                  .  '<tr class=" '.$cssClass.'"><td '.$css.'>-<td\><td '.$css.'>-<td\><td '.$css.'>-<td\><td '.$css.'>-<td\></tr></table>';
            return $html;
        }
        
        // Parse rows to hide to Array
        if(is_string($columnsToHide))
            $columnsToHide = [$columnsToHide];
        
        // Adding Filter 
        if(!in_array('filter', $columnsToHide)){
            $html .= '<div class="filter-elements">'
                  .  '<input type="text" id="table-filter" class="filter-of-table" placeholder="Search">'
                  .  '</div>';
        }

        // Verify Elements sent formation'
        $indexed = false;
        if(!empty($elements[0])){
            $indexed = true;
        }
        
        // Gets array keys and verify elements to get all Ids to insert in 'data-id' if there's a key 'id' in elements
        $dataElement = false;
        if($indexed){
            $keys = array_keys($elements[0]);
            if(in_array('id', $keys)){
                $dataElement = true;
                foreach($elements as $element){
                    $dataElementIds [] = $element['id'];
                }
            }
        }else{
            $keys = array_keys($elements);
            if(in_array('id', $keys)){
                $dataElement = true;
                foreach($elements as $element){
                    $dataElementIds = $elements['id'];
                }
            }
        }   
        // Positions controllers
        $position = [
            'position' => 0,
            'key'      => 0,
            'dataId'   => 0
        ];
        // Starts building the HTML
        $html .= '<table class="'.$cssClass.'" id="table-sectors">'
              . '<thead class="'.$cssClass.'">'
              . '<tr class="'.$cssClass.' header">';
        
        // Generating table head elements
        foreach($keys as $key){
            if(!is_null($columnsToHide) && count($columnsToHide) > 0){
                // loop throught $columnsToHide in order to search for matches
                while($position['position'] < count($columnsToHide)){
                    // verifying by column name
                    if($key == $columnsToHide[$position['position']]){
                        $html .= '<th class="'.$cssClass.' disabled" style="display:none">'.$key.'</th>';
                        $position['position'] = 100;
                    }
                    $position['position']++;
                }
                
                // reset $columnsToHide position controller for next loop
                if($position['position'] == count($columnsToHide)){
                    $position['position'] = 0;
                }
                
                // in case it didn't find any match to $columnsToHide
                if($position['position'] < 100){
                    $html .= '<th class="'.$cssClass.'">'.$key.'</th>';                
                }           
            }
            $position['position'] = 0;       
        }
        
        if(in_array('action', $columnsToHide)){
            $html .= '<th class="'.$cssClass.' disabled" style="display:none">Action</th>';
        }else{
            $html .= '<th class="'.$cssClass.'">Action</th>';
        }
        $html .= '</tr>';
       
        // In case of  indexed array ( $array [] = [])
        if($indexed){
            // Generating table body elements
            $html .= '</thead>'
                  .  '<tbody>';
            // Verifying whether to put the Data Element in Table Rows
            foreach($elements as $element){
                if($dataElement){
                    $html .= '<tr class="'.$cssClass.'" data-id="'.$dataElementIds[$position['dataId']].'">';
                }else{
                    $html .= '<tr class="'.$cssClass.'">';
                }
                
                while($position['key'] < count($keys)){
                    if(!is_null($columnsToHide) && count($columnsToHide) > 0){
                        // loop throught $columnsToHide in order to search for matches
                        while($position['position'] < count($columnsToHide)){
                            // verifying by column name
                            if($keys[$position['key']] == $columnsToHide[$position['position']]){
                                $html .= '<td class="'.$cssClass.' disabled" style="display:none">'.$element[$keys[$position['key']]].'</td>';
                                $position['position'] = 100;
                            }
                            $position['position']++;
                        }
                        
                        // reset $columnsToHide position controller for next loop
                        if($position['position'] == count($columnsToHide)){
                            $position['position'] = 0;
                        }
                        
                        // in case it didn't find any match to $columnsToHide
                        if($position['position'] < 100){
                            if(empty($element[$keys[$position['key']]])){
                                // Verify if TD will be clickable
                                if($addInsertButton){
                                    $html .= '<td class="'.$cssClass.'"><a class="edit-button</a>';
                                }else{
                                    $html .= '<td class="'.$cssClass.'">-</td>';
                                }
                            }else{
                                // Verify if TD will be clickable
                                if($addInsertButton){
                                    $html .= '<td class="'.$cssClass.'"><a class="edit-button">'.$element[$keys[$position['key']]].'</a></td>';
                                }else{
                                    $html .= '<td class="'.$cssClass.'">'.$element[$keys[$position['key']]].'</td>';
                                }
                            }
                        }
                        $position['position'] = 0;
                    }
                    $position['key']++;
                    
                }
                if($position['key'] == count($keys)){
                    $hideDelete = false;
                    // Verify if element is in use and can't be deleted
                    if(!empty($element['inUse'])){
                        $hideDelete = $element['inUse'];
                    }
                    // action buttons creation
                    if(in_array('action', $columnsToHide)){
                        $html .= '<td class="'.$cssClass.' disabled" style="display:none">'
                              .  '<a class="edit-button  disabled" style="display:none">Edit</a>';
                        if(!$hideDelete){
                            $html .= '<a class="delete-button disabled" style="display:none">Delete</a></td>';
                        }
                    }else{
                        $html .= '<td class="'.$cssClass.' actions">'
                              .  '<a class="edit-button">Edit</a>';
                        if(!$hideDelete){
                            $html .= '<a class="delete-button">Delete</a></td>';
                        }else{
                            $html .= '<a class="delete-button disabled">Delete</a></td>';
                        }
                    }
                    $html .= '</tr>';
                    $position['key'] = 0;
                    $position['dataId'] ++;
                }
            }
            $html .= '</tbody></table>';       
            return $html;
        }
        // In case of not indexed array ( $array = [])
        $html .= '<tr class="'.$cssClass.'" data-id="'.$elements['id'].'">';
        while($position['key'] < count($keys)){
            if(!is_null($columnsToHide) && count($columnsToHide) > 0){
                // loop throught $columnsToHide
                while($position['position'] < count($columnsToHide)){
                    // verifying by column name
                    if($keys[$position['key']] == $columnsToHide[$position['position']]){
                        $html .= '<td class="'.$cssClass.' disabled" style="display:none">'.$elements[$keys[$position['key']]].'</td>';
                        $position['position'] = 100;
                    }
                    $position['position']++;
                }
                
                // reset $columnsToHide position controller for next loop
                if($position['position'] == count($columnsToHide)){
                    $position['position'] = 0;
                }
                
                // in case it didn't find any match to $columnsToHide
                if($position['position'] < 100){
                    if(empty($elements[$keys[$position['key']]])){
                        $html .= '<td class="'.$cssClass.'">-</td>';
                    }else{
                        $html .= '<td class="'.$cssClass.'">'.$elements[$keys[$position['key']]].'</td>';   
                    }
                }
                $position['position'] = 0;
            }
            $position['key']++;
            
        }
        // action buttons creation
        if(in_array('action', $columnsToHide)){
            $html .= '<td class="'.$cssClass.' disabled" style="display:none">'
                  .  '<a class="waves-effect waves-teal btn-flat edit-text   disabled" id="edit-text"   style="display:none">Edit</a>'
                  .  '<a class="waves-effect waves-teal btn-flat delete-text disabled" id="delete-text" style="display:none">Delete</a></td>';
        }else{
            $html .= '<td class="'.$cssClass.' actions">'
                  .  '<a class="waves-effect waves-teal btn-flat edit-text" id="edit-text">Edit</a>'
                  .  '<a class="waves-effect waves-teal btn-flat delete-text" id="delete-text">Delete</a></td>';
        }
        $html .= '</tr>';
        
        return $html;
    } 
}