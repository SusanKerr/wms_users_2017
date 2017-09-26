# wms_users_2017
 C:\wamp\bin\apache\apache2.4.23\htdocs
 https://github.com/SusanKerr/wms_users_2017.git
 C:\wamp\bin\apache\apache2.4.23\htdocs\wms_users_2017
$ git status
On branch master
Your branch is up-to-date with 'origin/master'.

Changes not staged for commit:
 (use "git add <file>..." to update what will be committed)
 (use "git checkout -- <file>..." to discard changes in working directory)

       modified:   README.md

 no changes added to commit (use "git add" and/or "git commit -a")
 $ git add --all

sukerr@W15175 MINGW32 /c/wamp/bin/apache/apache2.4.23/htdocs/wms_users_2017 (master)
$ git status
On branch master
Your branch is up-to-date with 'origin/master'.

Changes to be committed:
  (use "git reset HEAD <file>..." to unstage)

        modified:   README.md
$ git add --all

sukerr@W15175 MINGW32 /c/wamp/bin/apache/apache2.4.23/htdocs/wms_users_2017 (master)
$ git status
On branch master
Your branch is up-to-date with 'origin/master'.

Changes to be committed:
  (use "git reset HEAD <file>..." to unstage)

        modified:   README.md


sukerr@W15175 MINGW32 /c/wamp/bin/apache/apache2.4.23/htdocs/wms_users_2017 (master)
$ git commit -m "changed README_1"
[master 3f6d934] changed README_1
 Committer: Kerr <sukerr@davidson.edu>
Your name and email address were configured automatically based
on your username and hostname. Please check that they are accurate.
You can suppress this message by setting them explicitly. Run the
following command and follow the instructions in your editor to edit
your configuration file:

    git config --global --edit

After doing this, you may fix the identity used for this commit with:

    git commit --amend --reset-author

 1 file changed, 15 insertions(+), 1 deletion(-)
 rewrite README.md (100%)

 $ git push origin master
Counting objects: 6, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (4/4), done.
Writing objects: 100% (6/6), 1.14 KiB | 1.14 MiB/s, done.
Total 6 (delta 1), reused 0 (delta 0)
remote: Resolving deltas: 100% (1/1), done.
To https://github.com/SusanKerr/wms_users_2017.git
   398038e..050dea0  master -> master

sukerr@W15175 MINGW32 /c/wamp/bin/apache/apache2.4.23/htdocs/wms_users_2017 (master)
$ git status
On branch master
Your branch is up-to-date with 'origin/master'.

nothing to commit, working tree clean

sukerr@W15175 MINGW32 /c/wamp/bin/apache/apache2.4.23/htdocs/wms_users_2017 (master)
$
another change

