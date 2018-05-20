# Contributing to Twill

## Reporting issues, giving feedback or proposing new features

We are using Pipefy to manage this projects through 3 different pipes:

- the **dev** pipe, where you can see what is being worked on a part of the current sprint
- the **roadmap** pipe, where you can see what is currently in our roadmap, wether it's been approved for future development or still being evaluated
- the **requests** pipe, where you can see and submit requests of all sorts: bug report, feature request, comments...

## Versioning scheme

Twill's versioning scheme maintains the following convention: `paradigm.major.minor`. Minor releases should never contain breaking changes.
When referencing Twill from your application, you should always use a version constraint such as `1.0.*`, since major releases of Twill do include breaking changes. We will most likely follow SemVer 2.0 (`major.minor.patch`) once we are public to make it easier for applications to update, but because we are going to change name without moving to 2.0, we need to follow this versionning scheme right now, which is actually the one Laravel itself is following. The VERSION file needs to be updated and a Git tag created to properly release a new version.

## Merge requests guidelines

- For any change, you should create a descriptively named branch from `master` and submit a merge request against `master`. No need for prefixes, but you can use some if they make sense (`fix-`, `hotfix-`, `ticket-number-`.)

- Send coherent history — make sure each individual commit in your pull request is meaningful. If you had to make a lot of intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

- PHP style: [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

- JavaScript style: [Standard](https://standardjs.com/), [Vue ESLint Essentials](https://github.com/vuejs/eslint-plugin-vue).

- This is a Laravel package so `master` isn't considered a production branch, in the sense that no one is deploying this standalone, but always as part of a Laravel application, which should use tags to choose a stable version. Hovewer, please avoid committing directly to the `master` branch. Prefer going through a Merge Request and get a review from someone else.

- If a fix needs to be released quickly and there are some un-released changes already merged on master or if you're not sure master is in a stable state for a new release right now, create your branch from the lastest available tag and submit a merge request again `master`. Once accepted, a new tag should be created using the head of your hotfix branch. This will allows us to release bug fixes while having un-released code in the `master` branch. If you want to read more about this approach, [check this article](https://hackernoon.com/a-branching-and-releasing-strategy-that-fits-github-flow-be1b6c48eca2). If you're not sure what to do anymore, just merge from `master` and submit a merge request against `master`, that's it :). The only case when you need to merge from a tag is in case of an emergency fix. Ping Quentin if you happen to have to do it for the first time!
