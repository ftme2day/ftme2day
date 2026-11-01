<?php
/**
 * Plugin Name: FTME2Day Analytics Tags
 * Description: Injects Google Tag Manager or GA4 (gtag.js) and pushes basic engagement events to the dataLayer.
 * Version: 1.0.0
 *
 * Configure in wp-config.php:
 *   define( 'FTME_GTM_ID', 'GTM-XXXXXXX' );   // preferred: manage tags inside GTM
 *   define( 'FTME_GA4_ID', 'G-XXXXXXXXXX' );  // used only when FTME_GTM_ID is not set
 */

defined( 'ABSPATH' ) || exit;

function ftme_analytics_enabled() {
	if ( is_admin() || is_customize_preview() ) {
		return false;
	}
	// Keep internal traffic out of the reports.
	return ! current_user_can( 'edit_posts' );
}

function ftme_analytics_id( $name ) {
	$const = 'FTME_' . $name . '_ID';
	return defined( $const ) ? constant( $const ) : '';
}

add_action( 'wp_head', function () {
	if ( ! ftme_analytics_enabled() ) {
		return;
	}
	$gtm = ftme_analytics_id( 'GTM' );
	$ga4 = ftme_analytics_id( 'GA4' );

	echo "<script>window.dataLayer=window.dataLayer||[];</script>\n";

	if ( $gtm ) {
		?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','<?php echo esc_js( $gtm ); ?>');</script>
<!-- End Google Tag Manager -->
		<?php
	} elseif ( $ga4 ) {
		?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga4 ); ?>"></script>
<script>function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?php echo esc_js( $ga4 ); ?>');</script>
		<?php
	}
}, 1 );

add_action( 'wp_body_open', function () {
	$gtm = ftme_analytics_id( 'GTM' );
	if ( $gtm && ftme_analytics_enabled() ) {
		printf(
			'<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=%s" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . "\n",
			esc_attr( $gtm )
		);
	}
} );

// Engagement events. With GTM these arrive as dataLayer events (create Custom Event triggers);
// with plain GA4 they are sent through gtag().
add_action( 'wp_footer', function () {
	if ( ! ftme_analytics_enabled() || ! ( ftme_analytics_id( 'GTM' ) || ftme_analytics_id( 'GA4' ) ) ) {
		return;
	}
	?>
<script>
(function(){
  var dl=window.dataLayer=window.dataLayer||[];
  var useGtag=!<?php echo ftme_analytics_id( 'GTM' ) ? 'true' : 'false'; ?>&&typeof window.gtag==='function';
  function send(name,params){
    params=params||{};
    if(useGtag){window.gtag('event',name,params);}
    else{params.event=name;dl.push(params);}
  }

  document.addEventListener('click',function(e){
    var a=e.target.closest&&e.target.closest('a[href]');
    if(!a)return;
    var h=a.getAttribute('href')||'';
    if(h.indexOf('tel:')===0){send('phone_click',{link_url:h});}
    else if(h.indexOf('mailto:')===0){send('email_click',{link_url:h});}
    else if(/\.(pdf|docx?|xlsx?|pptx?|zip|rar)(\?|#|$)/i.test(h)){send('file_download',{link_url:h,file_extension:h.split('?')[0].split('.').pop().toLowerCase()});}
    else if(a.host&&a.host!==location.host&&/^https?:/.test(a.href)){send('outbound_click',{link_url:a.href,link_domain:a.host});}
  },true);

  document.addEventListener('submit',function(e){
    var f=e.target;
    send('form_submit',{form_id:f.id||'',form_action:f.getAttribute('action')||location.pathname});
  },true);

  var marks={25:0,50:0,75:0,90:0};
  addEventListener('scroll',function(){
    var h=document.documentElement,max=h.scrollHeight-h.clientHeight;
    if(max<=0)return;
    var p=(h.scrollTop/max)*100;
    for(var k in marks){if(!marks[k]&&p>=k){marks[k]=1;send('scroll_depth',{percent_scrolled:+k});}}
  },{passive:true});
})();
</script>
	<?php
}, 99 );
