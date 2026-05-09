export interface Review {
    id: number;
    phone_id: number;
    user_id: number;
    comment: string;
    rating: number;
    created_at: string;
    updated_at: string;
}

export interface Phone {
    id: number;
    name: string;
    brand: string;
    model: string;
    price: number;
    image: string;
    store_url: string;
    ram: string;
    storage: string;
    camera: string;
    battery: string;
    screen_size: string;
    os: string;
    release_date: string;
    rating: number;
    created_at: string;
    updated_at: string;
    reviews: Review[];
}
