/**
 * Booking Page
 * Page for creating a new booking
 */

import BookingForm from '../components/BookingForm/BookingForm';

const BookingPage = () => {
  return (
    <div>
      <h1 className="text-3xl font-bold mb-8">Új Foglalás</h1>
      <BookingForm />
    </div>
  );
};

export default BookingPage;

