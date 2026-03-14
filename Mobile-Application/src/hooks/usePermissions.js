import { useContext } from 'react';
import { UserContext } from '../context/UserContext';

export const usePermissions = () => {
  const { user } = useContext(UserContext);
  const roles = Array.isArray(user?.roles) ? user.roles : (user?.roles && typeof user.roles === 'object' ? Object.values(user.roles) : []);
  const permissions = Array.isArray(user?.permissions) ? user.permissions : (user?.permissions && typeof user.permissions === 'object' ? Object.values(user.permissions) : []);

  const hasPermission = (permissionName) => {
    if (!permissionName) return false;
    if (!user) return false;
    // Admin role usually has all permissions, but checking specific permission is safer
    if (roles && roles.includes('Admin')) return true;
    return permissions.includes(permissionName);
  };

  const hasRole = (roleName) => {
    if (!roleName) return false;
    if (!user) return false;
    return roles.includes(roleName);
  };

  const hasAnyPermission = (permissionList) => {
    if (!user) return false;
    if (!Array.isArray(permissionList) || permissionList.length === 0) return false;
    if (roles && roles.includes('Admin')) return true;
    return permissionList.some(p => permissions.includes(p));
  };

  const hasAllPermissions = (permissionList) => {
    if (!user) return false;
    if (!Array.isArray(permissionList) || permissionList.length === 0) return false;
    if (roles && roles.includes('Admin')) return true;
    return permissionList.every(p => permissions.includes(p));
  };

  return {
    user,
    hasPermission,
    hasRole,
    hasAnyPermission,
    hasAllPermissions
  };
};
