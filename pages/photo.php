<?php
require_once( 'page.php' );

class Flogr_Photo extends Flogr_Page {

    var $photoId;
    var $info;
    var $licenses;
    var $comments;
    var $exif;
    var $geoLocation;
    var $context;
    var $sizes;
    
    function get_user_photos(
    $userId = null,
        $tags = null,
        $sort = null,
        $extras = null,
        $perPage = null,
        $page = null ) {
        $p = new Profiler();
        $photoSearchParams = array(
          "user_id"=>$userId ? $userId : $this->getconst('FLICKR_USER_ID'), 
          "tags"=>$tags ? $tags : $this->paramTags, 
          "sort"=>$sort ? $sort : $this->paramSort,
          "extras"=>$extras ? $extras : 'original_format,date_taken,date_upload', 
          "per_page"=>$perPage ? $perPage : $this->paramPerPage, 
          "page"=>$page ? $page : $this->paramPage);

        $this->photoList = $this->PhpFlickr->photos_search( $photoSearchParams );
        return $this->photoList;
    }
    
    function user_photos(
        $userId = null,
        $tags = null,
        $sort = null,
        $extras = null,
        $perPage = null,
        $page = null ) {
            $p = new Profiler();
            echo $photos = $this->get_user_photos($userId, $tags, $sort, $extras, $perPage, $page);
    }
    
    function get_user_favorite_photos( 
        $userId = null,
        $extras = null,
        $perPage = null,
        $page = null ) {
            $p = new Profiler();            
            $this->photoList = $this->PhpFlickr->favorites_getPublicList(
            $user ? $user : $this->getconst('FLICKR_USER_ID'),
            $extras ? $extras : FLOGR_PHOTO_EXTRAS, 
            $perPage ? $perPage : $this->paramPerPage,
            $page ? $page : $this->paramPage);
            return $this->photoList;
    }
    
    function user_favorite_photos( 
        $userId = null,
        $extras = null,
        $perPage = null,
        $page = null ) {
            $p = new Profiler();
            echo $this->get_user_favorite_photos($userId, $extras, $perPage, $page);
    }
    
    function get_group_photos( 
        $groupId = null, 
        $tags = null, 
        $userId = null, 
        $extras = null,
        $perPage = null, 
        $page = null ) {
        	$p = new Profiler();        
        	$this->photoList = $this->PhpFlickr->groups_pools_getPhotos(
             $groupId ? $groupId : $this->getconst('FLICKR_GROUP_ID'),
             $tags ? $tags : $this->paramTags,
             $userId ? $userId : $this->getconst('FLICKR_USER_ID'),
             $extras ? $extras : $this->getconst('FLOGR_PHOTO_EXTRAS'), 
             $perPage ? $perPage : $this->paramPerPage,
             $page ? $page : $this->paramPage);
    		return $this->photoList;
    }
    
    function group_photos( 
        $groupId = null, 
        $tags = null, 
        $userId = null, 
        $extras = null,
        $perPage = null, 
        $page = null ) {
        	$p = new Profiler();        	
            echo $this->get_group_photos($groupId, $tags, $userId, $extras, $perPage, $page);
    }
    
    function get_user_interestingness_photos( 
        $date = null,
        $extras = null,
        $perPage = null,
        $page = null) {
        	$p = new Profiler();        	
        	$this->photoList = $this->PhpFlickr->interestingness_getList(
            $date,
            $extras ? $extras : FLOGR_PHOTO_EXTRAS,
            $perPage ? $perPage : $this->paramPerPage,
            $page ? $page : $this->paramPage);
      		return $this->photoList;
    }
    
    function user_interestingness_photos( 
        $date = null,
        $extras = null,
        $perPage = null,
        $page = null) {
    		$p = new Profiler();
        	echo $this->get_user_interestingness_photos($date, $extras, $perPage, $page);
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    
    function get_photos() {
    	$p = new Profiler();
    	$photos = null;
    	if ( defined('FLICKR_GROUP_ID') ) {
        	$photos = $this->get_group_photos();
            if ( $photos ) $this->photoId = $photos['photo'][0]['id'];
        } else if ( defined('FLICKR_USER_ID') ) {
        	$photos = $this->get_user_photos();                
            if ( $photos ) $this->photoId = $photos['photo'][0]['id'];
        }
        return $photos;
    }
    
    function get_photo_id() {
    	$p = new Profiler();
    	if ( !$this->photoId ) {
            if ( $this->paramPhotoId ) {
                $this->photoId = $this->paramPhotoId;
            }
            else if ( defined('FLICKR_GROUP_ID') ) {
                // get first group photo
                $photos = $this->get_group_photos( null, FLOGR_TAGS_INCLUDE, null, '1', '1' );
                if ( $photos ) $this->photoId = $photos['photo'][0]['id'];
            }
            else if ( defined('FLICKR_USER_ID') ) {
                $photos = $this->get_user_photos( null, FLOGR_TAGS_INCLUDE, null, '1', '1' );                
                if ( $photos ) $this->photoId = $photos['photo'][0]['id'];
            }
        }
         
        return $this->photoId;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_photo_info( $photoId = null ) {
    	$p = new Profiler();
        $photoId = $photoId ? $photoId : $this->get_photo_id();
        if (!$this->info || $this->info['id'] != $photoId) {
            $this->info = $this->PhpFlickr->photos_getInfo( $photoId );
        }
        return $this->info;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_username( $photoId = null) {
    	$p = new Profiler();
    	$info = $this->get_photo_info( $photoId );
        return $info['owner']['username'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function username( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_username( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_realname( $photoId = null) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        return $info['owner']['realname'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function realname( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_realname( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_location( $photoId = null) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        return $info['owner']['location'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function location( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_location( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $this->PhpFlickrormat
     * @return unknown
     */
    function get_dateposted( $photoId = null, $format = null ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        $format = $format ? $format : FLOGR_DATE_FORMAT;
        return date( $format, $info['dates']['posted'] );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $this->PhpFlickrormat
     */
    function dateposted( $photoId = null, $format = null ) {
        $p = new Profiler();
        echo $this->get_dateposted( $photoId, $format );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $this->PhpFlickrormat
     * @return unknown
     */
    function get_datetaken( $photoId = null, $format = null ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        $format = $format ? $format : FLOGR_DATE_FORMAT;
        return date( $format, strtotime( $info['dates']['taken'] ) );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $this->PhpFlickrormat
     */
    function datetaken( $photoId = null, $format = null ) {
        $p = new Profiler();
        echo $this->get_datetaken( $photoId, $format );
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    function get_licenses() {
        $p = new Profiler();
        if ( !$this->licenses ) {
            $this->licenses = $this->PhpFlickr->photos_licenses_getInfo();
        }
         
        return $this->licenses;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_comments( $photoId = null ) {
        $p = new Profiler();
        $photoId = $photoId ? $photoId : $this->get_photo_id();
         
        if ( !$this->comments || $this->comments['photo_id'] != $photoId ) {
            $this->comments = $this->PhpFlickr->photos_comments_getList( $photoId );
        }

        return $this->comments;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_sizes( $photoId = null ) {
        $p = new Profiler();
        $photoId = $photoId ? $photoId : $this->get_photo_id();
        
        /**
         * HACK: Trying to not make additional calls if we already have the size
         * for this photo id.
         */
        if ( !$this->sizes || !strstr($this->sizes[0]['url'], $photoId) ) {
        	$this->sizes = $this->PhpFlickr->photos_getSizes( $photoId );
        }
                
        return $this->sizes;
    }

    function get_exif( $photoId = null ) {
        $p = new Profiler();
        $photoId = $photoId ? $photoId : $this->get_photo_id();
         
        if ( !$this->exif || $this->exif['photo']['id'] != $photoId ) {
            $this->exif = $this->PhpFlickr->photos_getExif( $photoId );
        }
        
        return $this->exif;
    }

    function exif( $photoId = null ) {
        $p = new Profiler();
        $exif = $this->get_exif( $photoId );
        $exifData = null;
        if ( $exif['exif'] ) {
            foreach ($exif['exif'] as $exifItem) 
            {
                if (stristr(FLOGR_EXIF, $exifItem['label']))
                {
                    $exifData .= "<tr>";
                    $exifData .= "<td>" . $exifItem['label'] . "</td>";
                    if ($exifItem['clean']) {
                        $exifData .= "<td>" . $exifItem['clean'] . "</td>";      
                    }
                    else {
                        $exifData .= "<td>" . $exifItem['raw'] . "</td>";
                    }
                    $exifData .= "</tr>";
                }
            }
            $geo = $this->get_geo_location( $photoId );
            if ($geo) $exifData .= "<tr><td>Location</td><td>" . $this->get_geo_location_link( $photoId ) . "</td></tr>";
            echo "<table>$exifData</table>";
        }
    }    

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_is_favorite( $photoId = null, $truetext = 'Y', $falsetext = 'N' ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        if ($info['isfavorite']) {
            return $truetext;
        } else {
            return $falsetext;
        }
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $truetext
     * @param unknown_type $this->PhpFlickralsetext
     */
    function is_favorite( $photoId = null, $truetext = 'Y', $falsetext = 'N' ) {
        $p = new Profiler();
        echo $this->get_is_favorite($photoId, $truetext, $falsetext);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_license_name( $photoId = null ) {
        $p = new Profiler();
        $licenses = $this->get_licenses();
        $info = $this->get_photo_info( $photoId );
        return $licenses[$info['license']]['name'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function license_name( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_license_name( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $inner
     * @return unknown
     */
    function get_license_link( $photoId = null, $inner = null ) {
        $p = new Profiler();
        $licenses = $this->get_licenses();
        $info = $this->get_photo_info( $photoId );
        $url = $licenses[$info['license']]['url'];
        $inner = $inner ? $inner : $this->get_license_name( $photoId );

        return "<a href='{$url}'>{$inner}</a>";
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $inner
     */
    function license_link( $photoId = null, $inner = null ) {
        $p = new Profiler();
        echo $this->get_license_link( $photoId, $inner );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_title( $photoId = null ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        return htmlspecialchars( $info['title'], ENT_QUOTES );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function title( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_title( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_description( $photoId = null ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        return htmlspecialchars($info['description'], ENT_QUOTES);
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function description( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_description( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $inner
     * @return unknown
     */
    function get_photopage_link( $photoId = null, $inner = 'Photopage' ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        $url = $info['urls']['url'][0]['_content'];
         
        return "<a href='{$url}'>{$inner}</a>";
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $inner
     */
    function photopage_link( $photoId = null, $inner = 'Photopage' ) {
        $p = new Profiler();
        echo $this->get_photopage_link( $photoId, $inner );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @return unknown
     */
    function get_comments_count( $photoId = null ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        return $info['comments'];
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     */
    function comments_count( $photoId = null ) {
        $p = new Profiler();
        echo $this->get_comments_count( $photoId );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $before
     * @param unknown_type $sep
     * @param unknown_type $after
     * @param unknown_type $commentLink
     * @param unknown_type $buddyIcon
     * @return unknown
     */    
    function get_comments_list(
    $photoId = null,
    $before = '<li>',
    $sep = ' says:<br/>',
    $after = '</li>',
    $commentLink = 'true',
    $buddyIcon = 'true' ) {
        $p = new Profiler();
        $comments = $this->get_comments( $photoId );
        if ( $comments['comment'] ) {
            $comment_list = null;
            foreach ( $comments['comment'] as $comment ) {
                $commentAuthor = "<a href='{$comment['permalink']}'><b>{$comment['authorname']}</b></a>";
                $commentText = $comment['_content'];
                $commentDate = "<br/><small>Posted on: " . date(FLOGR_DATE_FORMAT, $comment['datecreate']) . "</small>";
                $comment_list .= $before . $commentAuthor . $sep . $commentText . $commentDate . $after;
            }             
        }
        
        if ( $commentLink ) {
        	$comment_list .= $this->get_photopage_link( $photoId, "<br/>Leave a comment" );
        }
        
                 
        return $comment_list;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $before
     * @param unknown_type $sep
     * @param unknown_type $after
     * @param unknown_type $commentLink
     * @param unknown_type $buddyIcon
     */
    function comments_list( $photoId = null, $before = '<li>', $sep = ' says:<br/>', $after = '</li>', $commentLink = 'true', $buddyIcon = 'true' ) {
        $p = new Profiler();
        echo $this->get_comments_list( $photoId, $before, $sep, $after, $commentLink, $buddyIcon );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $before
     * @param unknown_type $after
     * @return unknown
     */
    function get_tags_list( $photoId = null, $before = '<li>', $after = '</li>' ) {
        $p = new Profiler();
        $info = $this->get_photo_info( $photoId );
        $tag_list = "";
        foreach ( $info['tags']['tag'] as $tag ) {
            $tag_list .= "{$before}<a href='index.php?type=recent&tags={$tag['_content']}'>{$tag['_content']}</a>{$after}";
        }
         
        return $tag_list;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $before
     * @param unknown_type $after
     */
    function tags_list( $photoId = null, $before = '<li>', $after = '</li>' ) {
        $p = new Profiler();
        echo $this->get_tags_list( $photoId, $before, $after );
    }
    
    function tags( $photoId = null, $before = '', $after = ' ' ) {
        $p = new Profiler();
        echo $this->get_tags_list( $photoId, $before, $after );
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $size
     * @return unknown
     */
    function get_img( $photoId = null, $quality = FLOGR_PHOTO_QUALITY, $scaleSize = null ) {
        $p = new Profiler();
        
        // build the photo url
        $src = $this->PhpFlickr->buildPhotoURL( $this->get_photo_info( $photoId ), $quality );
        
        /**
         * If $scaleSize is set scale the photo (maintaining 1:1 aspect ratio)
         */
        if ( $scaleSize ) {
        	$sizes = $this->get_sizes();
        	$size = 0;
        	foreach ( $sizes as $size ) {
        		if ( strtolower($size['label']) == strtolower($quality) ) break;        		
        	}
        	        	
        	$width = $size['width'];
        	$height = $size['height'];        	
        	if ( $width > $height ) {
        		$origWidth = $width;
        		$width = $scaleSize;
        		$height *= $scaleSize / $origWidth;
        	} else if ( $height > $width ) {
        		$origHeight = $height;
        		$height = $scaleSize;
        		$width *= $scaleSize / $origHeight;        		
        	}
        }
        $title = $this->get_title($photoId);
        $desc = $this->get_description($photoId);
        
        if ( $scaleSize ) {
        	return "<img class='photo' src='{$src}' height='{$height}' width='{$width}' title='{$title}' rel='{$desc}'/>";
        }

        return "<img class='photo' src='{$src}' title='{$title}' rel='{$desc}'/>";
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $photoId
     * @param unknown_type $quality
     * @param unknown_type $scaleSize
     */
    function img( $photoId = null, $quality = FLOGR_PHOTO_QUALITY, $scaleSize = null ) {
        $p = new Profiler();
        echo $this->get_img( $photoId, $quality, $scaleSize );
    }
    
    function add_comment( $id = null, $comment = null ) {
        $p = new Profiler();
        if ( $id == null || $comment == null ) return;
        $this->PhpFlickr->photos_comments_addComment( $id, $comment );
    }
    
    function get_geo_location( $id = null ) {
        $p = new Profiler();
        $id = $id ? $id : $this->get_photo_id();
        if ( !$this->geoLocation || $this->getLocation['id'] != $id ) {
            $this->geoLocation = $this->PhpFlickr->photos_geo_getLocation( $id );
        }
        return $this->geoLocation;
    }
    
    function geo_location( $id = null ) {
        $p = new Profiler();
        $geoLocation = $this->get_geo_location( $id );
        if ( $geoLocation ) {
         echo "{$geoLocation['location']['latitude']},{$geoLocation['location']['longitude']}";        	
        }
    }
    
    function get_geo_location_link( $id = null ) {
        $p = new Profiler();
        $geoLocation = $this->get_geo_location( $id );
        if ( $geoLocation ) {
         $title = $this->get_title( $id );
         $mapUrl = htmlspecialchars("http://maps.google.com/maps?q={$title}@{$geoLocation['location']['latitude']},{$geoLocation['location']['longitude']}", ENT_QUOTES);           
         return "<a target='_blank' href='{$mapUrl}'>{$geoLocation['location']['latitude']},{$geoLocation['location']['longitude']}</a>";          
        }
    }
    
    function get_context( $id = null ) {
        $p = new Profiler();
        $id = $id ? $id : $this->get_photo_id();
        if ( !$this->context || $this->context['id'] != $id ) {
            $this->context = $this->PhpFlickr->photos_getContext( $id );
        }
        return $this->context;
    }
    
    function get_previous_photo_id( $id = null ) {
        $p = new Profiler();
        $context = $this->get_context( $id );
        return $context['prevphoto']['id'];
    }
    
    function get_previous_photo_link( $id = null, $inner = 'prev' ) {
    	$prevId = $this->get_previous_photo_id( $id );
    	return "<a href='" . SITE_URL . "/index.php?photoId={$prevId}'>" . $inner . "</a>";
    }
    
    function previous_photo_link( $id = null, $inner = 'prev' ) {
    	echo $this->get_previous_photo_link( $id, $inner );
    }
        
    function get_next_photo_id( $id = null ) {
    	if ( !$this->photoId ) return;
    	
    	$p = new Profiler();
        $context = $this->get_context( $id );
        return $context['nextphoto']['id'];
    }

    function get_next_photo_link( $id = null, $inner = 'next' ) {
    	if ( !$this->photoId ) return;
    	
    	$nextId = $this->get_next_photo_id( $id );
    	return "<a href='" . SITE_URL . "/index.php?photoId={$nextId}'>" . $inner . "</a>";
    }
    
    function next_photo_link( $id = null, $inner = 'next' ) {
    	echo $this->get_next_photo_link( $id, $inner );
    }
}
?>

<?php $photo = new Flogr_Photo(); ?>