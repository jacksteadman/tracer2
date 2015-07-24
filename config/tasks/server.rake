# lib/capistrano/tasks/server.cap
namespace :server do
	desc 'Restart apache'
	task :restart do
		on roles(:web, :api), in: :sequence, wait: 2 do
			execute :sudo, "/sbin/service httpd graceful"
		end
	end

	desc 'Temporay Fix for Server issue'
	task :fix_prepend_issue do
		on roles(:web, :api, :worker) do
			execute "sudo sed -e '/runkit_function_redefine(/ s/^/#/' -i /etc/bluestate/php/auto_prepend.php"
		end
	end

	task :fixRevert do
		on roles(:web, :api, :worker) do
			execute "sudo cp -rf /etc/bluestate/php/auto_test /etc/bluestate/php/auto_prepend.php"
		end
	end

	# desc 'Check Server Uptime'
	# task :uptime do
	# 	on roles(:web, :api, :worker) do
	# 		execute "uptime"
	# 	end
	# end

	# desc 'Put Server in Maintenance Mode for Deploy'
	# task :down do
	# 	on roles(:web, :api) do
	# 		within release_path do
	# 			execute :php, "artisan down"
	# 		end
	# 	end
	# end

	# desc 'Put Application up after deploy'
	# task :up do
	# 	on roles(:web, :api) do
	# 		within release_path do
	# 			execute :php, "artisan up"
	# 		end
	# 	end
	# end

	# desc 'Set Environment to Production'
	# task :fixEnv do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			# execute "sed -e '/local/ s/^/ ?: production/' -i bootstrap/start.php"
	# 			execute "sed -i 's/local/production/' bootstrap/start.php"
	# 		end
	# 	end
	# end

	# desc "Tail application log"
	# task :tail_log do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute :php, "artisan tail"
	# 		end
	# 	end
	# end

	# desc "Tail application log"
	# task :tail_log do
	# 	on roles(:web, :api, :worker) do
	# 		within release_path do
	# 			execute :php, "artisan tail"
	# 		end
	# 	end
	# end
end
