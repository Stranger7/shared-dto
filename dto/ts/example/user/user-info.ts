import type { Role } from '../role';
import type { Team } from '../team';

export type UserInfo = {
  // Имя пользователя
  firstName?: string;

  // Фамилия пользователя
  lastName?: string;

  // E-mail пользователя
  email?: string;

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

  // Команда
  team?: Team;
}
