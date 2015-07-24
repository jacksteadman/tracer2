# Laravel Install || Update
# lib/capistrano/tasks/laravel.cap
namespace :laravel do

	desc 'Setup hook task'
	task :setup do
		on roles(:web, :api, :worker) do
		end
	end

	# desc 'Make sure directories containing linked files exist in shared path'
	# task :touch_linked_files do
	# 	on roles(:web, :api, :worker) do
	# 		within shared_path do
	# 			execute :mkdir, '-p app/config && touch app/config/welcome.php'
	# 		end
	# 	end
	# end

	desc "Setup Laravel log file"
	task :setup_log do
		on roles(:web, :api, :worker) do
			within release_path do
				execute :touch, "app/storage/logs/laravel.log"
			end
		end
	end

	desc "Setup Laravel folder permissions"
	task :set_permissions do
		on roles(:web, :app) do
			within release_path do
				execute :chmod, "u+x artisan"
				execute :sudo, :chmod, "-R 2777 app/storage/cache"
				execute :sudo, :chmod, "-R o+w  app/storage/logs"
				execute :sudo, :chmod, "-R 2777 app/storage/meta"
				execute :sudo, :chmod, "-R 2777 app/storage/sessions"
				execute :sudo, :chmod, "-R 2777 app/storage/views"
				execute :sudo, :chown, "--recursive ec2-user:nobody public/uploads/"
				execute :sudo, :chown, "--recursive ec2-user:nobody app/storage"
			end
		end
	end

	desc "Run Laravel Artisan migrate task."
	task :migrate do
		on roles(:last_web) do
			within release_path do
				execute :php, "artisan migrate --env=#{fetch(:env)}"
			end
		end
	end

	after :setup, :setup_log
	after :setup_log, :set_permissions
	after :set_permissions, :migrate

	# desc "Fix Environment configuration on deploy"
	# task :fix_config do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			# set :staged, fetch(:stage)
	# 			execute "cp -f #{release_path}/app/config/#{fetch(:stage)}/cache.php #{release_path}/app/config/cache.php"

	# 			execute "cp -f #{release_path}/app/config/#{fetch(:stage)}/database.php #{release_path}/app/config/database.php"
	# 		end
	# 	end
	# end


	# desc "Set permissions on the uploads directory after uploads"
	# task :setup_uploads do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute :mkdir, "-p public/uploads/users"
	# 			execute :chmod, "-R 777 public/uploads"
	# 		end
	# 	end
	# end

	# after :setup, :setup_log
	# after :setup_log, :set_permissions
	# after :set_permissions, :setup_uploads

	# desc 'Clean up build files on servers'
	# task :cleanup do
	# 	on roles(:web, :api, :worker) do
	# 		execute "rm #{release_path}/public/Gruntfile.js"
	# 		execute "rm #{release_path}/public/package.json"
	# 		execute "rm #{release_path}/public/readme.md"
	# 		execute "rm -r #{release_path}/public/source"
	# 		execute "rm -r #{release_path}/public/scss"
	# 		execute "rm -r #{release_path}/public/tmpl"
	# 		execute "rm -r #{release_path}/.idea"

	# 	end
	# end


	# desc "Set permissions on the uploads directory before uploads"
	# task :preUploadDir do
	# 	on roles(:app, :api, :worker) do
	# 		within release_path do
	# 			execute "sudo chown --recursive ec2-user:nobody public/uploads/"
	# 			execute "sudo chown --recursive ec2-user:nobody public/uploads/users"
	# 		end
	# 	end
	# end

	# desc "Prepare Laravel folder permissions for deploy"
	# task :prepare do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute "sudo chown --recursive ec2-user:nobody app/storage/cache"
	# 			execute "sudo chown --recursive ec2-user:nobody public/uploads"
	# 		end
	# 	end
	# end


	# desc "Set permission on cache folder."
	# task :cachePerms do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute "cd #{release_path}/app/storage && sudo chown --recursive ec2-user:nobody cache > /dev/null 2>&1"
	# 			execute "cd #{release_path}/app/storage && chown -R ec2-user:nobody cache && chmod -R o+w cache"
	# 		end
	# 	end
	# end

	# desc "Clear Laravel Cache"
	# task :clear_cache do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute "cd #{release_path}/app/storage && sudo chown -R ec2-user:nobody cache > /dev/null 2>&1"
	# 			execute :rm, "-rf app/storage/cache/*"
	# 		end
	# 	end
	# end

	# desc "Optimize Laravel Class Loader"
	# task :optimize do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute :php, "artisan clear-compiled"
	# 			execute :php, "artisan optimize -o"
	# 		end
	# 	end
	# end


	# desc "Run Laravel Artisan DB Seed Task"
	# task :seed do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute :php, "artisan db:seed"
	# 		end
	# 	end
	# end

end # End of Laravel namespace
