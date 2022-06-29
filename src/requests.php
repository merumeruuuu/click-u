<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// session_destroy();
class Utensils {
    protected $requests_contents = array();

    public function __construct(){
        // get the  request array from the session
        $this->request_contents = !empty($_SESSION['request_contents'])?$_SESSION['request_contents']:NULL;
        if ($this->request_contents === NULL){
            // set some base values
            $this->request_contents = array( 'total_items' => 0);
        }
    }

    /**
     * request Contents: Returns the entire request array
     * @param    bool
     * @return    array
     */
    public function contents(){
        // rearrange the newest first
        $requests = array_reverse($this->request_contents);

        // remove these so they don't create a problem when showing the request table
        unset($requests['total_items']);
        unset($requests['requested_total']);

        return $requests;
    }

    /**
     * Get request item: Returns a specific request item details
     * @param    string    $row_id
     * @return    array
     */
    public function get_item($row_id){
        return (in_array($row_id, array('total_items'), TRUE) OR ! isset($this->request_contents[$row_id]))
            ? FALSE
            : $this->request_contents[$row_id];
    }

    /**
     * Total Items: Returns the total item count
     * @return    int
     */
    public function total_items(){
        return $this->request_contents['total_items'];
    }



    /**
     * Insert items into the request and save it to the session
     * @param    array
     * @return    bool
     */
    public function insert($item = array()){
        if(!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if(!isset($item['id'],$item['storageID'], $item['name'], $item['category'], $item['qty'])){
                return FALSE;
            }else{
                /*
                 * Insert Item
                 */
                // prep the quantity
                $item['qty'] = (float) $item['qty'];
                if($item['qty'] == 0){
                    return FALSE;
                }
                // prep the category
                $item['category'] =  $item['category'];
                // create a unique identifier for the item being inserted into the request
                $rowid = $item['id'];
                // get quantity if it's already there and add it on
                $old_qty = isset($this->request_contents[$rowid]['qty']) ? (int) $this->request_contents[$rowid]['qty'] : 0;
                // re-create the entry with unique identifier and updated quantity
                $item['rowid'] = $rowid;
                $item['qty'] += $old_qty;
                $this->request_contents[$rowid] = $item;

                // save request Item
                if($this->save_request()){
                    return isset($rowid) ? $rowid : TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }

    /**
     * Update the request
     * @param    array
     * @return    bool
     */
    public function update($item = array()){
        if (!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if (!isset($item['rowid'], $this->request_contents[$item['rowid']])){
                return FALSE;
            }else{
                // prep the quantity
                if(isset($item['qty'])){
                    $item['qty'] = (int) $item['qty'];
                    // remove the item from the request, if quantity is zero
                    if ($item['qty'] == 0){
                        unset($this->request_contents[$item['rowid']]);
                        return TRUE;
                    }
                }

                // find updatable keys
                $keys = array_intersect(array_keys($this->request_contents[$item['rowid']]), array_keys($item));

                // product id & name shouldn't be changed
                foreach(array_diff($keys, array('id','storageID', 'name','category')) as $key){
                    $this->request_contents[$item['rowid']][$key] = $item[$key];
                }
                // save request data
                $this->save_request();
                return TRUE;
            }
        }
    }

    /**
     * Save the request array to the session
     * @return    bool
     */
    protected function save_request(){
        $this->request_contents['total_items'] = $this->request_contents['requested_total'] = 0;
        foreach ($this->request_contents as $key => $val){
            // make sure the array contains the proper indexes
            if(!is_array($val) OR !isset($val['category'], $val['qty'])){
                continue;
            }


            $this->request_contents['total_items'] += $val['qty'];

        }

        // if request empty, delete it from the session
        if(count($this->request_contents) <= 2){
            unset($_SESSION['request_contents']);
            return FALSE;
        }else{
            $_SESSION['request_contents'] = $this->request_contents;
            return TRUE;
        }
    }

    /**
     * Remove Item: Removes an item from the request
     * @param    int
     * @return    bool
     */
     public function remove($row_id){
        // unset & save
        unset($this->request_contents[$row_id]);
        $this->save_request();
        return TRUE;
     }
      // clear request
     public function clear(){

        $this->request_contents = array( 'total_items' => 0);
        unset($_SESSION['request_contents']);
     }

    /**
     * Destroy the request: Empties the request and destroy the session
     * @return    void
     */
    public function destroy(){
        $this->request_contents = array( 'total_items' => 0);
        unset($_SESSION['request_contents']);
    }
}
