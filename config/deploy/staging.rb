set :stage, :staging
set :env, 'staging'

set :deploy_to, '/var/www/sites/tracer.dev.bsd.net'

role :app, %w{
}
role :web, %w{
  web-wpdsvc-001.dev.bsd.net
  web-wpdsvc-002.dev.bsd.net
}
role :last_web, %w{
  web-wpdsvc-002.dev.bsd.net
}
role :api, %w{
}
role :worker, %w{
}
role :db,  %w{
}

