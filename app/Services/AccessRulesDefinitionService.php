<?php
namespace App\Services;

class AccessRulesDefinitionService
{
  const ROLES = ['Owner+Admin', 'Admin', 'Member', 'Candidate', 'Matchmaker','Member Admin'];
  const USER_TYPES = ['Candidate','Representative','Matchmaker'];

  public function getValidRoles(){
      return self::ROLES;
  }

  public function getValidUserTypes(){
      return self::USER_TYPES;
  }

  public function hasRoleChangeRights(){
      return [self::ROLES[0]];
  }

  public function hasRemoveMemberRights(){
    //   return [self::ROLES[0],self::ROLES[1],self::ROLES[3],self::ROLES[4],self::ROLES[5]];
      return [self::ROLES[0],self::ROLES[1]];
  }

  public function hasDeleteTeamRights(){
    //   return [self::ROLES[0],self::ROLES[3],self::ROLES[4]];
      return [self::ROLES[0],self::ROLES[1]];
  }

  public function hasRespondConnectionRequestRights(){
      return [self::ROLES[0],self::ROLES[1]];
  }

  public function hasDisconnectionRights(){
        return [self::ROLES[0],self::ROLES[1]];
  }
}
