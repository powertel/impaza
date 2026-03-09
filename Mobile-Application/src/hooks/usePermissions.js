import { useContext } from 'react';
import { UserContext } from '../context/UserContext';

export const usePermissions = () => {
  const { user } = useContext(UserContext);

  const hasPermission = (permissionName) => {
    if (!user || !user.permissions) return false;
    // Admin role usually has all permissions, but checking specific permission is safer
    if (user.roles && user.roles.includes('Admin')) return true; 
    return user.permissions.includes(permissionName);
  };

  const hasRole = (roleName) => {
    if (!user || !user.roles) return false;
    return user.roles.includes(roleName);
  };

  const hasAnyPermission = (permissions) => {
    if (!user || !user.permissions) return false;
    if (user.roles && user.roles.includes('Admin')) return true;
    return permissions.some(p => user.permissions.includes(p));
  };

  const hasAllPermissions = (permissions) => {
    if (!user || !user.permissions) return false;
    if (user.roles && user.roles.includes('Admin')) return true;
    return permissions.every(p => user.permissions.includes(p));
  };

  return {
    user,
    hasPermission,
    hasRole,
    hasAnyPermission,
    hasAllPermissions
  };
};
