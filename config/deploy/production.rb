set :stage, :production
set :env, 'production'

role :app, %w{
}
role :web, %w{
  web-wpdsvc-gen-001.wpd.bsd.net
  web-wpdsvc-gen-002.wpd.bsd.net
}
role :last_web, %w{
  web-wpdsvc-gen-002.wpd.bsd.net
}
role :api, %w{
}
role :worker, %w{  
  worker-qd-001.wpd.bsd.net
  worker-qd-002.wpd.bsd.net
}
role :db,  %w{
}

