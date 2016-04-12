<?php
function getAllActiveOfferType()
{
    $rarray = array();
    $offer_types = findByConditionArray(array('is_active' => 1),'offer_types');
    if(!empty($offer_types))
    {
        $rarray = array("type" => "success", "data" => $offer_types);
    }
    else
    {
        $rarray = array("type" => "error", "message" => "No offer type found.");
    }
    echo json_encode($rarray);
    exit;
}
?>