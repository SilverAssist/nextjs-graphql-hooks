<?php
/**
 * NextJS GraphQL Hooks Updater - GitHub Updates Integration
 *
 * Integrates the reusable silverassist/wp-github-updater package for automatic updates.
 * Provides seamless WordPress admin updates without requiring authentication tokens.
 *
 * @package NextJSGraphQLHooks
 * @since 1.0.0
 * @author Silver Assist
 * @version 1.0.2
 * @license GPL v2 or later
 */

namespace NextJSGraphQLHooks;

// Prevent direct access
defined("ABSPATH") or exit;

use SilverAssist\WpGithubUpdater\Updater as GitHubUpdater;
use SilverAssist\WpGithubUpdater\UpdaterConfig;

/**
 * Class Updater
 *
 * Integrates the reusable GitHub updater package for NextJS GraphQL Hooks plugin.
 */
class Updater extends GitHubUpdater
{
  /**
   * Initialize the updater with NextJS GraphQL Hooks specific configuration
   *
   * @param string $plugin_file Full path to main plugin file
   * @param string $github_repo GitHub repository in format "owner/repo"
   */
  public function __construct(string $plugin_file, string $github_repo)
  {
    $config = new UpdaterConfig(
      $plugin_file,
      $github_repo,
      [
        "plugin_name" => "NextJS GraphQL Hooks",
        "plugin_description" => "WordPress plugin that provides essential GraphQL queries for NextJS sites using WordPress as a headless CMS",
        "plugin_author" => "Silver Assist",
        "plugin_homepage" => "https://github.com/SilverAssist/nextjs-graphql-hooks",
        "requires_wordpress" => "6.0",
        "requires_php" => "8.0",
        "asset_pattern" => "nextjs-graphql-hooks-v{version}.zip",
        "cache_duration" => 12 * 3600, // 12 hours
        "ajax_action" => "nextjs_graphql_hooks_check_version",
        "ajax_nonce" => "nextjs_graphql_hooks_ajax"
      ]
    );
    
    parent::__construct($config);
  }
}
