import type { UserCreate } from './user-create';

export type UserUpdate = UserCreate & {
  id: number;
}
