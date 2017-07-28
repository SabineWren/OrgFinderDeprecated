<?php

namespace thalos_api;

abstract class QuerySystems
{
    const Accounts = 'accounts';
    const Organizations = 'organizations';
    const Forums = 'forums';
}

abstract class APISources
{
    const Cache = 'cache';
    const Live = 'live';
}

abstract class AccountActions
{
    const Dossier = 'dossier';
    const Memberships = 'memberships';
    const Forum_Profile = 'forum_profile';
    const Threads = 'threads';
    const Posts = 'posts';
    const Full_Profile = 'full_profile';
    const All_Accounts = 'all_accounts';
}

abstract class OrganizationActions
{
    const All_Organizations = 'all_organizations';
    const Single_Organization = 'single_organization';
    const Organization_Members = 'organization_members';
}

abstract class ForumActions
{
    const Posts = 'posts';
    const Threads = 'threads';
    const Forums = 'forums';
}

abstract class OrganizationSources
{
    const RSI = 'rsi';
    const Wikia = 'wikia';
}

abstract class AccountSources
{
    const RSI = 'rsi';
}

abstract class ForumSources
{
    const RSI = 'rsi';
}

abstract class MaxLimits
{
    const RSI_All_Organizations = 255;
    const Cache_All_Organizations = 1000;
    const Cache_All_Accounts = 1000;
}

