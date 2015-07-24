# config valid only for Capistrano 3.1
lock '3.2.1'

set :application, 'tracer2'
set :repo_url, 'git@github.com:jacksteadman/tracer2.git'

ask :branch, proc { `git rev-parse --abbrev-ref HEAD`.chomp }.call

set :deploy_to, '/var/www/sites/tracer.wpd.bsd.net'
set :file_owner, 'ec2-user'

set :scm, :git
set :format, :pretty
set :log_level, :debug
set :keep_releases, 5

set :linked_dirs, %w{
  storage/cache
  storage/logs
  storage/meta
  storage/sessions
  storage/views
  vendor
}

# Hipchat notifications
#set :hipchat_token, "35e01bf391d31ea25e1125e3dfba2a"
#set :hipchat_room_name, "Universal Quick Donate"
#set :hipchat_announce, true
#set :hipchat_color, 'blue' #normal message color
#set :hipchat_success_color, 'green' #finished deployment message color
#set :hipchat_failed_color, 'red' #cancelled deployment message color
#set :hipchat_message_format, 'html'
#set :hipchat_options, {
#  :api_version  => "v2" # Set "v2" to send messages with API v2
#}

# set :default_env, { path: "/opt/ruby/bin:$PATH" }

namespace :deploy do
  task :bootstrap do
    on roles(:web,:worker) do
      execute "/usr/bin/sudo mkdir -p #{deploy_to} && /usr/bin/sudo chown #{fetch(:file_owner)}:#{fetch(:file_owner)} #{deploy_to}"
    end
  end

  desc 'Restart application'
  task :restart do
    on roles(:worker) do
      execute "/usr/bin/sudo /sbin/service supervisord restart"
    end
  end

  before :check, :bootstrap

  after :updated, "composer:install"
  after :updated, "laravel:setup"
  after :publishing, :restart
end
