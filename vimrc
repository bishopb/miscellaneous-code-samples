" extra bindings
map <F11> :se nu!<CR>
set pastetoggle=<F12>

" display
colorscheme darkblue
set background=dark
syntax on

" sound
set noerrorbells
set novisualbell
set t_vb=
set tm=500

" filetypes
autocmd FileType html setlocal expandtab softtabstop=4 tabstop=4 shiftwidth=4 tabstop=4
autocmd FileType php setlocal expandtab softtabstop=4 tabstop=4 shiftwidth=4 tabstop=4
autocmd FileType xml setlocal expandtab softtabstop=4 tabstop=4 shiftwidth=4 tabstop=4
autocmd FileType xslt setlocal expandtab softtabstop=4 tabstop=4 shiftwidth=4 tabstop=4

" printing
set popt=left:36pt,right:36pt,top:36pt,bottom:36pt

" backups
set nobackup
if has("win32") || has("win16")
  set backupdir=%TMP%,.
  set directory=%TMP%,.
else
  set backupdir=$HOME/.vim/tmp,/tmp,.
  set directory=$HOME/.vim/tmp,/tmp,.
endif
