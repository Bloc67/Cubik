<?php

/**
 * @name      ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 */

function template_init()
{
	return array(
		'use_default_images' => 'never',
		'require_font-awesome' => false,
		'theme_version' => '1.0',
		'require_theme_strings' => false,
		'theme_variants' => array('lite','light'),
		'avatars_on_indexes' => 1,
		'menu_numeric_notice' => array(
			0 => ' <span class="pm_indicator">%1$s</span>',
			1 => ' <span>[<strong>%1$s</strong>]</span>',
			2 => ' <span>[<strong>%1$s</strong>]</span>',
		),
		'page_index_template' => array(
			'base_link' => '<li class="linavPages"><a class="navPages" href="{base_link}" role="menuitem">%2$s</a></li>',
			'previous_page' => '<span class="previous_page" role="menuitem">{prev_txt}</span>',
			'current_page' => '<li class="linavPages"><strong class="current_page" role="menuitem">%1$s</strong></li>',
			'next_page' => '<span class="next_page" role="menuitem">{next_txt}</span>',
			'expand_pages' => '<li class="linavPages expand_pages" role="menuitem" {custom}> <a href="#">...</a> </li>',
			'all' => '<span class="linavPages all_pages" role="menuitem">{all_txt}</span>',
		),
		'mentions' => array('mentioner_template' => '<a href="{mem_url}" class="mentionavatar">{avatar_img}{mem_name}</a>'),
        // to populate by the theme
        'callback_entries' => array()
	);
}

function call_template_callbacks($id, $array)
{
    global $settings;

	if (empty($array))
		return;

	foreach ($array as $callback)
	{
		$func = 'template_' . $id . '_' . $callback;
		if (function_exists($func)) {
			echo '<div class="' . $func . '">';
			$func();
			echo '</div>';
	        $settings[] = $id . '_' . $callback;
	    }
	}
}

function template_html_above()
{
	global $context, $scripturl, $txt, $modSettings;

	echo '<!DOCTYPE html>
<html', $context['right_to_left'] ? ' dir="rtl"' : '', '>
<head>
	<title>', $context['page_title_html_safe'], '</title>
	<meta charset="UTF-8" />';

	if (isBrowser('ie'))
		echo '
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />';

	echo '
	<meta name="viewport" content="width=device-width" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta name="description" content="', $context['page_title_html_safe'], '" />';

	if (!empty($modSettings['enableOpenID']))
		echo '
	<meta http-equiv="x-xrds-location" content="' . $scripturl . '?action=xrds" />';

	if (!empty($context['robot_no_index']))
		echo '
	<meta name="robots" content="noindex" />';

	template_css();

	if (!empty($context['canonical_url']))
		echo '
	<link rel="canonical" href="', $context['canonical_url'], '" />';

	echo '
	<link rel="shortcut icon" sizes="196x196" href="' . $context['favicon'] . '" />
	<link rel="help" href="', $scripturl, '?action=help" />
	<link rel="contents" href="', $scripturl, '" />', ($context['allow_search'] ? '
	<link rel="search" href="' . $scripturl . '?action=search" />' : '');

	if (!empty($context['newsfeed_urls']))
		echo '
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['rss'], '" href="', $context['newsfeed_urls']['rss'], '" />
	<link rel="alternate" type="application/rss+xml" title="', $context['forum_name_html_safe'], ' - ', $txt['atom'], '" href="', $context['newsfeed_urls']['atom'], '" />';

	if (!empty($context['links']['next']))
		echo '
	<link rel="next" href="', $context['links']['next'], '" />';
	elseif (!empty($context['current_topic']))
		echo '
	<link rel="next" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=next" />';

	if (!empty($context['links']['prev']))
		echo '
	<link rel="prev" href="', $context['links']['prev'], '" />';
	elseif (!empty($context['current_topic']))
		echo '
	<link rel="prev" href="', $scripturl, '?topic=', $context['current_topic'], '.0;prev_next=prev" />';

	if (!empty($context['current_board']))
		echo '
	<link rel="index" href="', $scripturl, '?board=', $context['current_board'], '.0" />';

	theme()->template_javascript();
	theme()->template_inlinecss();
	
	echo $context['html_headers'];
	echo '
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
</head>
<body id="', $context['browser_body_id'], '" class="action_', !empty($context['current_action']) ? htmlspecialchars($context['current_action'], ENT_COMPAT, 'UTF-8') : (!empty($context['current_board']) ?
	'messageindex' : (!empty($context['current_topic']) ? 'display' : 'home')),
	!empty($context['current_board']) ? ' board_' . htmlspecialchars($context['current_board'], ENT_COMPAT, 'UTF-8') : '', '">';
}

function template_body_above()
{
	global $context, $settings, $scripturl, $txt;

	$tchecks = tchecks();

//	echo '<pre>' , print_r($context['linked_calendar_events']) , '</pre>';

	echo '
	<header id="top_section"' , $context['user']['is_logged'] ? '' : ' class="isguest"' , '>
		<aside>', call_template_callbacks('th', $context['theme_header_callbacks'], ''), '</aside>
		<h1 id="forumtitle">
			<a class="forumlink" href="', $scripturl, '"><img src="', !empty($settings['header_logo_url']) ? $settings['header_logo_url'] : $settings['images_url'].'/Cubik2021logo.png' , '" alt="" /></a>
		</h1>
		', template_menu() , '
		', template_menu(true) , '
	</header>
	<div id="wrapper">
        <div class="wrapper linktrees">' , theme_linktree() , '</div>
		<aside id="upper_section" class="wrapper">' 
	      , call_template_callbacks('uc', $context['upper_content_callbacks']),'
		</aside>
		<div id="main_content_section"' . $tchecks . '>';
	
}

function template_th_login_bar()
{
	global $context, $modSettings, $txt, $scripturl;

	echo '
			<div id="top_section_notice" class="user">
				<form action="', $scripturl, '?action=login2;quicklogin" method="post" accept-charset="UTF-8" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>
					<div id="password_login">
						<input type="text" name="user" size="10" class="input_text" placeholder="', $txt['username'], '" />
						<input type="password" name="passwrd" size="10" class="input_password" placeholder="', $txt['password'], '" />
						<select name="cookielength">
							<option value="60">', $txt['one_hour'], '</option>
							<option value="1440">', $txt['one_day'], '</option>
							<option value="10080">', $txt['one_week'], '</option>
							<option value="43200">', $txt['one_month'], '</option>
							<option value="-1" selected="selected">', $txt['forever'], '</option>
						</select>
						<input type="submit" value="', $txt['login'], '" />
						<input type="hidden" name="hash_passwrd" value="" />
						<input type="hidden" name="old_hash_passwrd" value="" />
						<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
						<input type="hidden" name="', $context['login_token_var'], '" value="', $context['login_token'], '" />';

	if (!empty($modSettings['enableOpenID']))
		echo '
						<a class="icon icon-margin i-openid" href="', $scripturl, '?action=login;openid" title="' . $txt['openid'] . '"><s>' . $txt['openid'] . '"</s></a>';
	echo '
					</div>
				</form>
			</div>';
}

function template_th_search_bar()
{
	global $context, $modSettings, $txt, $scripturl;

	echo '
			<form id="search_form" action="', $scripturl, '?action=search;sa=results" method="post" accept-charset="UTF-8">
				<input type="text" name="search" id="quicksearch" value="" class="input_text" placeholder="', $txt['search'], '" />';

	// Using the quick search dropdown?
	if (!empty($modSettings['search_dropdown']))
	{
		$selected = !empty($context['current_topic']) ? 'current_topic' : (!empty($context['current_board']) ? 'current_board' : 'all');

		echo '
				<select name="search_selection" id="search_selection">
					<option value="all"', ($selected == 'all' ? ' selected="selected"' : ''), '>', $txt['search_entireforum'], ' </option>';

		// Can't limit it to a specific topic if we are not in one
		if (!empty($context['current_topic']))
			echo '
					<option value="topic"', ($selected == 'current_topic' ? ' selected="selected"' : ''), '>', $txt['search_thistopic'], '</option>';

		// Can't limit it to a specific board if we are not in one
		if (!empty($context['current_board']))
			echo '
					<option value="board"', ($selected == 'current_board' ? ' selected="selected"' : ''), '>', $txt['search_thisbrd'], '</option>';

		if (!empty($context['additional_dropdown_search']))
			foreach ($context['additional_dropdown_search'] as $name => $engine)
				echo '
					<option value="', $name, '">', $engine['name'], '</option>';

		echo '
					<option value="members"', ($selected == 'members' ? ' selected="selected"' : ''), '>', $txt['search_members'], ' </option>
				</select>';
	}

	// Search within current topic?
	if (!empty($context['current_topic']))
		echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_topic' : 'topic'), '" value="', $context['current_topic'], '" />';
	// If we're on a certain board, limit it to this board ;).
	if (!empty($context['current_board']))
		echo '
				<input type="hidden" name="', (!empty($modSettings['search_dropdown']) ? 'sd_brd[' : 'brd['), $context['current_board'], ']"', ' value="', $context['current_board'], '" />';

	echo '
				<button type="submit" name="search;sa=results" class="', (!empty($modSettings['search_dropdown'])) ? 'with_select' : '', '"><i class="icon i-search icon-shade"></i></button>
				<input type="hidden" name="advanced" value="0" />
			</form>';
}

function template_uc_news_fader()
{
	global $settings, $context, $txt;

	// Display either news fader and random news lines (not both). These now run most of the same mark up and CSS. Less complication = happier n00bz. :)
	if (!empty($settings['enable_news']) && !empty($context['random_news_line']))
	{
		echo '
			<div id="news">
				<h2>', $txt['news'], '</h2>';

		template_news_fader();

		echo '
			</div>';
	}
}

function template_body_below()
{
	global $context, $txt;

	echo '
		</div>
	</div>
	<footer id="footer_section">
		<ul class="foot">
			<li class="copyright">',
				theme_copyright(), '
			</li>',
			!empty($context['newsfeed_urls']['rss']) ? '<li>
				<a id="button_rss" href="' . $context['newsfeed_urls']['rss'] . '" class="rssfeeds new_win"><i class="icon icon-margin i-rss icon-big"><s>' . $txt['rss'] . '</s></i></a>
			</li>' : '',
			$context['show_load_time'] ? '<li>
				'. (sprintf($txt['page_created_full'], $context['load_time'], $context['load_queries'])).'
			</li>' : '',			
		'</ul>';
	
	// call this if exists
	if(function_exists('c_sidebarbuttons'))
		c_sidebarbuttons();
}

function template_html_below()
{
	global $context;

	echo '
	</footer>';

	// load in any javascript that could be deferred to the end of the page
	theme()->template_javascript(true);

	// Anything special to put out?
	if (!empty($context['insert_after_template']))
		echo $context['insert_after_template'];

	echo '
</body>
</html>';
}

function theme_linktree($default = 'linktree')
{
	global $context, $settings, $txt;

	if (empty($context[$default]))
		return;

	echo '
			<nav>
				<ul class="navigate_section">';

	foreach ($context[$default] as $pos => $tree)
	{
		echo '
					<li>
						<span>';

		if (isset($tree['extra_before']))
			echo $tree['extra_before'];

		echo $settings['linktree_link'] && isset($tree['url']) ? '<a href="' . $tree['url'] . '">' .$tree['name']. '</a>' : $tree['name'];

		if (isset($tree['extra_after']))
			echo $tree['extra_after'];

		echo '
						</span>
					</li>';
	}

	echo '
				</ul>
			</nav>';
}

function template_menu( $separate = false )
{
	global $context, $txt;

	$separate_menu = array('profile','pm','mentions');
	
	// WAI-ARIA a11y tweaks have been applied here.
	echo '
			' , !$separate ? '
				<input type="checkbox" id="mobilmeny" aria-controls="primary-menu" aria-expanded="false" />
				<label id="mobilmeny_label" for="mobilmeny"><i class="icon i-list icon-big"></i></label>' : '' , '
				<nav id="menu_nav' , $separate ? '_sep' : '' , '">
					<ul id="main_menu' , $separate ? '_sep' : '' , '" role="menubar">';

	foreach ($context['menu_buttons'] as $act => $button)
	{
	    if($separate && !in_array($act,$separate_menu))
            continue;
	    elseif(!$separate && in_array($act,$separate_menu))
            continue;

		echo '
						<li id="button_', $act, '" class="listlevel1', !empty($button['sub_buttons']) ? ' subsections" aria-haspopup="true"' : '"', '>
							<a class="linklevel1', !empty($button['active_button']) ? ' active' : '', (!empty($button['indicator']) ? ' indicator' : ''), '" href="', $button['href'], '" ', isset($button['target']) ? 'target="' . $button['target'] . '"' : '', '>', (!empty($button['data-icon']) ? '<i class="icon icon-menu icon-lg ' . $button['data-icon'] . '" title="' . (!empty($button['alttitle']) ? $button['alttitle'] : $button['title']) . '"></i> ' : ''), '<span class="button_title" aria-hidden="true">', $button['title'], '</span>
							</a>';

		// Any 2nd level menus?
		if (!empty($button['sub_buttons']))
		{
			echo '
							<ul class="menulevel2" role="menu">';

			foreach ($button['sub_buttons'] as $childact => $childbutton)
			{
				echo '
								<li id="button_', $childact, '" class="listlevel2', !empty($childbutton['sub_buttons']) ? ' subsections" aria-haspopup="true"' : '"', '>
									<a class="linklevel2" href="', $childbutton['href'], '" ', isset($childbutton['target']) ? 'target="' . $childbutton['target'] . '"' : '', '>', $childbutton['title'], '</a>';

				// 3rd level menus :)
				if (!empty($childbutton['sub_buttons']))
				{
					echo '
									<ul class="menulevel3" role="menu">';

					foreach ($childbutton['sub_buttons'] as $grandchildact => $grandchildbutton)
						echo '
										<li id="button_', $grandchildact, '" class="listlevel3">
											<a class="linklevel3" href="', $grandchildbutton['href'], '" ', isset($grandchildbutton['target']) ? 'target="' . $grandchildbutton['target'] . '"' : '', '>', $grandchildbutton['title'], '</a>
										</li>';

					echo '
									</ul>';
				}

				echo '
								</li>';
			}

			echo '
							</ul>';
		}

		echo '
						</li>';
	}

	echo '
					</ul>
				</nav>';

}

function template_button_strip($button_strip, $direction = '', $strip_options = array())
{
	global $context, $txt;

	// Not sure if this can happen, but people can misuse functions very efficiently
	if (empty($button_strip))
		return '';

	if (!is_array($strip_options))
		$strip_options = array();

	// List the buttons in reverse order for RTL languages.
	if ($context['right_to_left'])
		$button_strip = array_reverse($button_strip, true);

	// Create the buttons... now with cleaner markup (yay!).
	$buttons = array();
	foreach ($button_strip as $key => $value)
	{
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '
								<li role="menuitem"><a' . (isset($value['id']) ? ' id="button_strip_' . $value['id'] . '"' : '') . ' class="linklevel1 button_strip_' . $key . (!empty($value['active']) ? ' active' : '') . '" href="' . $value['url'] . '"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>' . $txt[$value['text']] . '</a></li>';
	}

	// No buttons? No button strip either.
	if (empty($buttons))
		return '';

	echo '
							<ul role="menubar" class="buttonlist', !empty($direction) ? ' float' . $direction : '', (empty($buttons) ? ' hide"' : '"'), (!empty($strip_options['id']) ? ' id="' . $strip_options['id'] . '"' : ''), '>
								', implode('', $buttons), '
							</ul>';
}

function template_quickbutton_strip($strip, $tests = array())
{
	global $options;

	$buttons = array();

	foreach ($strip as $key => $value)
	{
		if (isset($value['checkbox']))
		{
			if (!empty($value['checkbox']) && ((!empty($options['display_quick_mod']) && $options['display_quick_mod'] == 1) || $value['checkbox'] === 'always'))
			{
				$buttons[] = '
						<li class="listlevel1 ' . $key . '">
							<input class="input_check ' . $key . '_check" type="checkbox" name="' . $value['name'] . '[]" value="' . $value['value'] . '" />
						</li>';
			}
		}
		elseif (!isset($value['test']) || !empty($tests[$value['test']]))
		{
			if (!empty($value['override']))
			{
				$buttons[] = $value['override'];
			}
			else
			{
				$buttons[] = '
						<li class="listlevel1">
							<a href="' . $value['href'] . '" class="linklevel1 ' . $key . '_button"' . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '>' . $value['text'] . '</a>
						</li>';
			}
		}
	}

	// No buttons? No button strip either.
	if (empty($buttons))
	{
		return '';
	}

	echo '
					<ul class="quickbuttons">', implode('
						', $buttons), '
					</ul>';
}

function template_basicicons_legend()
{
	global $context, $modSettings, $txt;

	echo '
		<p class="floatleft">', !empty($modSettings['enableParticipation']) && $context['user']['is_logged'] ? '
			<span class="topicicon i-profile"></span> ' . $txt['participation_caption'] : '<span class="topicicon img_normal"> </span>' . $txt['normal_topic'], '<br />
			' . (!empty($modSettings['pollMode']) ? '<span class="topicicon i-poll"> </span>' . $txt['poll'] : '') . '
		</p>
		<p>
			<span class="topicicon i-locked"> </span>' . $txt['locked_topic'] . '<br />
			<span class="topicicon i-sticky"> </span>' . $txt['sticky_topic'] . '<br />
		</p>';
}

function template_show_error($error_id)
{
	global $context;

	if (empty($error_id))
		return;

	$error = isset($context[$error_id]) ? $context[$error_id] : array();

	echo '
					<div id="', $error_id, '" class="', (isset($error['type']) ? ($error['type'] === 'serious' ? 'errorbox' : 'warningbox') : 'successbox'), empty($error['errors']) ? ' hide"' : '"', '>';

	// Optional title for our results
	if (!empty($error['title']))
		echo '
						<dl>
							<dt>
								<strong id="', $error_id, '_title">', $error['title'], '</strong>
							</dt>
							<dd>';

	// Everything that went wrong, or correctly :)
	if (!empty($error['errors']))
	{
		echo '
								<ul', (isset($error['type']) ? ' class="error"' : ''), ' id="', $error_id, '_list">';

		foreach ($error['errors'] as $key => $err)
			echo '
									<li id="', $error_id, '_', $key, '">', $err, '</li>';
		echo '
								</ul>';
	}

	// All done
	if (!empty($error['title']))
		echo '
							</dd>
						</dl>';

	echo '
					</div>';
}

function template_uc_generic_infobox()
{
	global $context;

	if (empty($context['generic_infobox']))
	{
		return;
	}

	foreach ($context['generic_infobox'] as $key)
	{
		template_show_error($key);
	}
}


function template_pagesection($button_strip = false, $strip_direction = '', $options = array(), $extraclass = '', $id="")
{
	global $context;

	// Hmmm. I'm a tad wary of having floatleft here but anyway............
	// @todo - Try using table-cell display here. Should do auto rtl support. Less markup, less css. :)
	if (!empty($options['page_index_markup']))
		$pages = '<ul ' . (isset($options['page_index_id']) ? 'id="' . $options['page_index_id'] . '" ' : '') . 'class="pagelinks floatleft" role="menubar">' . $options['page_index_markup'] . '</ul>';
	else
	{
		if (!isset($options['page_index']))
			$options['page_index'] = 'page_index';
		$pages = empty($context[$options['page_index']]) ? '' : '<ul ' . (isset($options['page_index_id']) ? 'id="' . $options['page_index_id'] . '" ' : '') . 'class="pagelinks floatleft" role="menubar">' . $context[$options['page_index']] . '</ul>';
	}

	if (!isset($options['extra']))
		$options['extra'] = '';

	echo '
			<nav' , !empty($id) ? ' id="'.$id.'"' : '' , ' class="pagesection' , !empty($extraclass) ? ' '.$extraclass : '' , '">
				', $pages, '
				', !empty($button_strip) && !empty($context[$button_strip]) ? template_button_strip($context[$button_strip], $strip_direction) : '',
	$options['extra'], '
			</nav>';
}

function template_news_fader()
{
	global $settings, $context;

	echo '
		<ul id="elkFadeScroller">
			<li>
				', $settings['enable_news'] == 2 ? implode('</li><li>', $context['news_lines']) : $context['random_news_line'], '
			</li>
		</ul>';

	addInlineJavascript('
		$(\'#elkFadeScroller\').Elk_NewsFader(' . (empty($settings['newsfader_time']) ? '' : '{\'iFadeDelay\': ' . $settings['newsfader_time'] . '}') . ');', true);
}

function template_member_online($member, $link = true)
{
	global $context;

	return ((!empty($context['can_send_pm']) && $link) ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['text'] . '">' : '') .
		   '<i class="' . ($member['online']['is_online'] ? 'iconline' : 'icoffline') . '" title="' . $member['online']['text'] . '"></i>' .
		   ((!empty($context['can_send_pm']) && $link) ? '</a>' : '');
}

function template_member_email($member, $text = false)
{
	global $context, $txt, $scripturl;

	if ($context['can_send_email'])
	{
		if ($text)
		{
			if ($member['show_email'] === 'no_through_forum')
			{
				return '<a class="linkbutton" href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $member['id'] . '">' . $txt['email'] . '</a>';
			}
			elseif ($member['show_email'] === 'yes_permission_override' || $member['show_email'] === 'yes')
			{
				return '<a class="linkbutton" href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $member['id'] . '">' . $member['email'] . '</a>';
			}
			else
			{
				return $txt['hidden'];
			}
		}
		else
		{
			if ($member['show_email'] !== 'no')
			{
				return '<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $member['id'] . '" class="icon i-envelope-o' . ($member['online']['is_online'] ? '' : '-blank') . '" title="' . $txt['email'] . ' ' . $member['name'] . '"><s>' . $txt['email'] . ' ' . $member['name'] . '</s></a>';
			}
			else
			{
				return '<i class="icon i-envelope-o" title="' . $txt['email'] . ' ' . $txt['hidden'] . '"><s>' . $txt['email'] . ' ' . $txt['hidden'] . '</s></i>';
			}
		}
	}

	return '';
}

function template_msg_email($id, $member = false)
{
	global $context, $txt, $scripturl;

	if ($context['can_send_email'])
	{
		if ($member === false || $member['show_email'] != 'no')
		{
			if (empty($member['id']))
			{
				return '<a href="' . $scripturl . '?action=emailuser;sa=email;msg=' . $id . '" class="icon i-envelope-o' . (($member !== false && $member['online']['is_online']) ? '' : '-blank') . '" title="' . $txt['email'] . '"><s>' . $txt['email'] . '</s></a>';
			}
			else
			{
				return '<a href="' . $scripturl . '?action=emailuser;sa=email;uid=' . $member['id'] . '" class="icon i-envelope-o' . (($member !== false && $member['online']['is_online']) ? '' : '-blank') . '" title="' . $txt['email'] . '"><s>' . $txt['email'] . '</s></a>';
			}
		}
		else
		{
			return '<i class="icon i-envelope-o" title="' . $txt['email'] . ' ' . $txt['hidden'] . '"><s>' . $txt['email'] . ' ' . $txt['hidden'] . '</s></i>';
		}
	}

	return '';
}

function tchecks()
{
	global $context,$settings;
	
	$checks = array('is_poll','calendar_post');
	$hits = array();

	foreach($checks as $a => $b)
	if(!empty($context[$b]))
		$hits[] = $b;
	
	if(count($hits)>0)
		return ' class="' . (implode(' ',$hits)) . '"';
}
