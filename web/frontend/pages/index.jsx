import { useNavigate, TitleBar, Loading } from '@shopify/app-bridge-react';
import { Card, EmptyState, Layout, Page, SkeletonBodyText } from '@shopify/polaris';
import { CustomerCard, ProductCard } from '../components';

export default function HomePage() {
  /*
  Add an App Bridge useNavigate hook to set up the navigate function.
  This function modifies the top-level browser URL so that you can
  navigate within the embedded app and keep the browser in sync on reload.
  */
  const navigate = useNavigate()
  
  /*
    These are mock values. Setting these values lets you preview the loading markup and the empty state.
  */
    const isLoading = false;
    const isRefetching = true;
    const Customers = ['yes'];
    const customerMarkup = Customers?.length ? (
      <CustomerCard Customers={Customers} loading={isRefetching} />
    ): null;

    const Products = ['yes'];
    const productsMarkup = Products?.length ? (
      <ProductCard Products = {Products} loading={isRefetching} />
    ): null;
  
    /* loadingMarkup uses the loading component from AppBridge and components from Polaris  */
  const loadingMarkup = isLoading ? (
    <Card sectioned>
      <Loading />
      <SkeletonBodyText />
    </Card>
  ) : null;

  /* Use Polaris Card and EmptyState components to define the contents of the empty state */
  const emptyStateMarkup =
    !isLoading && (!Customers?.length || !Products?.length) ? (
    // !isLoading && !QRCodes?.length ? (
      <Card sectioned>
        <EmptyState
          heading="Get customer/product data"
          /* This button will update the customer and product data */
          action={{
            content: 'Get the data',
            // onAction () => navigate('/qrcodes/new'),
            onAction: () => console.log('action'),
          }}
          image="https://cdn.shopify.com/s/files/1/0262/4071/2726/files/emptystate-files.png"
        >
          <p>
            Display customer and product data.
          </p>
        </EmptyState>
      </Card>
    ) : null;
  /*
    Use Polaris Page and TitleBar components to create the page layout,
    and include the empty state contents set above.
  */
  return (
    <Page>
      <TitleBar
        title="Customer and Product Data"
        primaryAction={{
          content: 'Update customer/product data',
            // onAction () => navigate('/qrcodes/new'),
            onAction: () => console.log('action'),
        }}
      />
      <Layout>
        <Layout.Section>
          {loadingMarkup}
          {emptyStateMarkup}
          {customerMarkup}
          {productsMarkup}
        </Layout.Section>
      </Layout>
    </Page>
  );
}
