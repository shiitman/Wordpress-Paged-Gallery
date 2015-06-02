<?php 
/*
Plugin Name: Wordpress Paged Gallery
Description: This plugin lets you to add a simple pagination to default WP gallery without changing core files. May be managed via shortcodes. Initial idea found here: http://stackoverflow.com/questions/23093441/default-wordpress-gallery-pagination 
Plugin URI: https://gist.github.com/shiitman/5f3e19a0b116bc09edb2
Author URI: https://shiitman.net
Author: Alexander Wolodarskij
*/

add_filter('post_gallery', 'wp_paged_gallery', 10, 2);

function wp_paged_gallery($output, $attr ) 
{
    global $post;

    //GALLERY SETUP STARTS HERE----------------------------------------//

    if (isset($attr['orderby'])) {
        $attr['orderby'] = sanitize_sql_orderby($attr['orderby']);
        if (!$attr['orderby'])
            unset($attr['orderby']);
    }
   
    extract(shortcode_atts(array(
        'paged' => false,
        'per_page' => 12,
        'order' => 'ASC',
        'orderby' => 'menu_order ID',
        'id' => $post->ID,
        'itemtag' => 'dl',
        'icontag' => 'dt',
        'captiontag' => 'dd',
        'columns' => 3,
        'link'=>'file',
        'size' => 'thumbnail',
        'ids' => '',
        'include' => 'none',
        'exclude' => ''
    ), $attr));


   if (!$paged)
      return;
    $id = intval($id);

   if ($include!=$ids && $ids!="")
       $include=$ids;

    if ('RAND' == $order) $orderby = 'none';
    
        $include = preg_replace('/[^0-9,]+/', '', $include);
        $_attachments = get_posts(array( 'exclude' => $exclude, 'exclude' => $include, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby, "post_parent"=>$id, 'posts_per_page' => -1));

        $attachments = array();
        foreach ($_attachments as $key => $val) {
            $attachments[$val->ID] = $_attachments[$key];
        }
   
    //GALLERY SETUP END HERE------------------------------------------//

    //PAGINATION SETUP START HERE-------------------------------------//
    $current = (get_query_var('paged')) ? get_query_var( 'paged' ) : 1;
    $per_page=  intval($per_page);
    //$offset = ($page-1) * $per_page;
    $offset = ($current-1) * $per_page;

    $total = sizeof($attachments);
    $total_pages = round($total/$per_page);
    if($total_pages < ($total/$per_page))
    {   $total_pages = $total_pages+1;
    }
    //PAGINATION SETUP END HERE-------------------------------------//


     //GALLERY OUTPUT START HERE---------------------------------------//
    $output = '<div id="gallery-1" class="gallery galleryid-'.$post->ID.' gallery-columns-'.$columns.' gallery-size-'.$size.'">';
    $counter = 0;
    $pos = 0;
    foreach ($attachments as $id => $attachment) 
    {   $pos++;
       if(($counter < $per_page)&&($pos > $offset))
        {   $counter++;
            $largetitle = get_the_title($attachment->ID);

            $currentlink = ($link=='file')?wp_get_attachment_image_src($id, 'full') : (($link=='none')? "": get_permalink($id));  
            $currentlink=($link=='file')?$currentlink[0]:$currentlink;
            
            $img = wp_get_attachment_image_src($id, $size);        
            $output .= "<".$itemtag." class='gallery-item'><".$icontag." class='gallery-icon'><a href=\"{$currentlink}\"  title=\"{$largetitle}\"><img src=\"{$img[0]}\" width=\"{$img[1]}\" height=\"{$img[2]}\" alt=\"{$largetitle}\" /></a></".$icontag."></".$itemtag.">\n";
        }

    }  
    $output .= "<div class=\"clear\"></div>\n";
    $output .= "</div>\n";
    //GALLERY OUTPUT ENDS HERE---------------------------------------//


    //PAGINATION OUTPUT START HERE-------------------------------------//
    $output .= paginate_links( array(
        'format' => '?paged=%#%',
        'current' => $current,
        'total' => $total_pages,
        'prev_text'    => __('&laquo;'),
        'next_text'    => __('&raquo;')
    ) );
    //PAGINATION OUTPUT ENDS HERE-------------------------------------//



    return $output;
}
?>
