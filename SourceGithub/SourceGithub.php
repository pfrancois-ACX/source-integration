<?php
# Copyright (C) 2008	John Reese
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.

if ( false === include_once( config_get( 'plugin_path' ) . 'Source/MantisSourcePlugin.class.php' ) ) {
	return;
}

class SourceGithubPlugin extends MantisSourcePlugin {
	function register() {
		$this->name = lang_get( 'plugin_SourceGithub_title' );
		$this->description = lang_get( 'plugin_SourceGithub_description' );

		$this->version = '0.1';
		$this->requires = array(
			'MantisCore' => '1.2.0',
			'Source' => '0.9a',
		);

		$this->author = 'John Reese';
		$this->contact = 'jreese@leetcode.net';
		$this->url = 'http://leetcode.net';
	}

	function get_types( $p_event ) {
		return array( 'github' => lang_get( 'plugin_SourceGithub_github' ) );
	}

	function show_type( $p_event, $p_type ) {
		if ( 'github' == $p_type ) {
			return lang_get( 'plugin_SourceGithub_github' );
		}
	}

	function show_changeset( $p_event, $p_repo, $p_changeset ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		$t_ref = substr( $p_changeset->revision, 0, 8 );
		$t_branch = $p_changeset->branch;

		return "$p_repo->name $t_ref ($t_branch)";
	}

	function show_file( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		return  "$p_action - $p_file->filename";
	}

	function url_repo( $p_event, $p_repo, $t_changeset=null ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		$t_username = $p_repo->info['hub_username'];
		$t_reponame = $p_repo->info['hub_reponame'];

		if ( !is_null( $t_changeset ) ) {
			$t_ref = "/$t_changeset->revision";
		}

		return "http://github.com/$t_username/$t_reponame/tree$t_ref";
	}

	function url_changeset( $p_event, $p_repo, $p_changeset ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		$t_username = $p_repo->info['hub_username'];
		$t_reponame = $p_repo->info['hub_reponame'];
		$t_ref = "$t_changeset->revision";

		return "http://github.com/$t_username/$t_reponame/commit/$t_ref";
	}

	function url_file( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		$t_username = $p_repo->info['hub_username'];
		$t_reponame = $p_repo->info['hub_reponame'];
		$t_ref = "$t_changeset->revision";
		$t_filename = $t_file->filename;

		return "http://github.com/$t_username/$t_reponame/tree/$t_ref/$t_filename";
	}

	function url_diff( $p_event, $p_repo, $p_changeset, $p_file ) {
		if ( 'github' != $p_repo->type ) {
			return $p_repo;
		}

		$t_username = $p_repo->info['hub_username'];
		$t_reponame = $p_repo->info['hub_reponame'];
		$t_ref = "$t_changeset->revision";
		$t_filename = $t_file->filename;

		return "http://github.com/$t_username/$t_reponame/commit/$t_ref";
	}

	function update_repo_form( $p_event, $p_repo ) {
		if ( 'github' != $p_repo->type ) {
			return;
		}

		if ( isset( $p_repo->info['hub_username'] ) ) {
			$t_username = $p_repo->info['hub_username'];
		}
		if ( isset( $p_repo->info['hub_reponame'] ) ) {
			$t_username = $p_repo->info['hub_reponame'];
		}
?>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceGithub_hub_username' ) ?></td>
<td><input name="hub_username" maxlength="250" size="40" value="<?php echo $t_hub_username ?>"/></td>
</tr>
<tr <?php echo helper_alternate_class() ?>>
<td class="category"><?php echo lang_get( 'plugin_SourceGithub_hub_reponame' ) ?></td>
<td><input name="hub_reponame" maxlength="250" size="40" value="<?php echo $t_hub_reponame ?>"/></td>
</tr>
<?php
	}

	function update_repo( $p_event, $p_repo ) {
		if ( 'github' != $p_repo->type ) {
			return;
		}

		$f_hub_username = gpc_get_string( 'hub_username' );
		$f_hub_reponame = gpc_get_string( 'hub_reponame' );

		$p_repo->info['hub_username'] = $f_hub_username;
		$p_repo->info['hub_reponame'] = $f_hub_reponame;

		return $p_repo;
	}

	function commit( $p_event, $p_repo, $p_data ) {
	}

	function import_repo( $p_event, $p_repo ) {
		if ( 'github' != $p_repo->type ) {
			return;
		}
		echo '<pre>';

		$t_uri_base = 'http://github.com/api/v1/json/' .
			urlencode( $p_repo->info['hub_username'] ) . '/' .
			urlencode( $p_repo->info['hub_reponame'] ) . '/';
		$t_json = file_get_contents( $t_uri_base . 'commits/master' );

		$t_data = json_decode( $t_json, true );
		var_dump( $t_data );
		die();
	}
}
