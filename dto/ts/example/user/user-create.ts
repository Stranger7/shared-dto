import type { Role } from '../role';

export type UserCreate = {
  // Имя пользователя
  firstName: string;

  // Фамилия пользователя
  lastName: string;

  // E-mail пользователя
  email: string;

  // Дата рождения
  birthDate?: string;

  // Роли
  roles?: Role[];

  // Активен
  isActive?: boolean;

  // Баланс
  balance?: number;

  // Биометрические данные
  bio?: {};
}
